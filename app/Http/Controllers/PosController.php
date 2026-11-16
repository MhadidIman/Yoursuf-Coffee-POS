<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Tampilkan halaman utama Kasir (POS) Yoursuf Coffee.
     */
    public function index(): View
    {
        $categories = Category::orderBy('id')->get();

        $products = Product::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $orders = Order::with('items')
            ->where(function ($query) {
                $query->whereIn('status', ['masuk', 'dibuat'])
                    ->orWhereDate('created_at', today());
            })
            ->orderByRaw("CASE status WHEN 'masuk' THEN 1 WHEN 'dibuat' THEN 2 WHEN 'selesai' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->get();

        $lowStockIngredients = Ingredient::whereColumn('current_stock', '<=', 'min_stock_alert')->get();

        $cupStock = Ingredient::where('name', 'like', '%Cup%')
            ->where('name', 'not like', 'Lid%')
            ->first();
        $lidStock = Ingredient::where('name', 'like', '%Lid%')->first();

        return view('pos.index', compact(
            'categories',
            'products',
            'orders',
            'lowStockIngredients',
            'cupStock',
            'lidStock'
        ));
    }

    /**
     * Ambil daftar antrean pesanan terbaru dalam format JSON.
     */
    public function getOrders(): JsonResponse
    {
        $orders = Order::with('items')
            ->where(function ($query) {
                $query->whereIn('status', ['masuk', 'dibuat'])
                    ->orWhereDate('created_at', today());
            })
            ->orderByRaw("CASE status WHEN 'masuk' THEN 1 WHEN 'dibuat' THEN 2 WHEN 'selesai' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Simpan transaksi pesanan baru dari Kasir POS.
     * - Mendukung Diskon / Promo Bundling (discount_amount)
     * - Langsung memotong otomatis stok Cup 16oz dan Lid (Tutup Cup) sesuai jumlah cup yang terjual.
     */
    public function storeOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_type' => ['required', 'in:takeaway,delivery'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_wa' => ['nullable', 'string', 'max:30'],
            'delivery_area' => ['nullable', 'string', 'max:100'],
            'delivery_fee' => ['nullable', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris'],
            'cash_received' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.item_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $freeAreas = ['UMY', 'UAA', 'UNJAYA', 'Krapyak'];
        $orderType = $validated['order_type'];
        $deliveryArea = $orderType === 'delivery' ? ($validated['delivery_area'] ?? 'UMY') : null;

        $deliveryFee = 0;
        if ($orderType === 'delivery') {
            if (in_array($deliveryArea, $freeAreas, true)) {
                $deliveryFee = 0;
            } else {
                $deliveryFee = (int) ($validated['delivery_fee'] ?? 0);
            }
        }

        $discountAmount = max(0, (int) ($validated['discount_amount'] ?? 0));

        return DB::transaction(function () use ($validated, $orderType, $deliveryArea, $deliveryFee, $discountAmount) {
            $itemsSubtotal = 0;
            $totalCups = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $cartItem) {
                $product = Product::findOrFail($cartItem['product_id']);

                if (! $product->is_available) {
                    abort(422, "Produk {$product->name} sedang habis / tidak tersedia.");
                }

                $qty = (int) $cartItem['quantity'];
                $price = (int) $product->price;
                $costPrice = (int) ($product->cost_price ?: round($price * 0.5));
                $lineSubtotal = $price * $qty;

                $itemsSubtotal += $lineSubtotal;
                $totalCups += $qty;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'quantity' => $qty,
                    'item_notes' => ! empty($cartItem['item_notes']) ? trim($cartItem['item_notes']) : null,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $effectiveDiscount = min($discountAmount, $itemsSubtotal + $deliveryFee);
            $totalAmount = max(0, ($itemsSubtotal + $deliveryFee) - $effectiveDiscount);
            $paymentMethod = $validated['payment_method'];

            if ($paymentMethod === 'cash') {
                $cashReceived = isset($validated['cash_received']) && $validated['cash_received'] > 0
                    ? (int) $validated['cash_received']
                    : $totalAmount;

                if ($cashReceived < $totalAmount) {
                    abort(422, 'Nominal uang tunai yang diterima kurang dari total pembayaran.');
                }

                $changeAmount = $cashReceived - $totalAmount;
            } else {
                $cashReceived = $totalAmount;
                $changeAmount = 0;
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'order_type' => $orderType,
                'customer_name' => ! empty($validated['customer_name']) ? trim($validated['customer_name']) : null,
                'customer_wa' => ! empty($validated['customer_wa']) ? trim($validated['customer_wa']) : null,
                'delivery_area' => $deliveryArea,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $effectiveDiscount,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'cash_received' => $cashReceived,
                'change_amount' => $changeAmount,
                'status' => 'masuk',
                'notes' => ! empty($validated['notes']) ? trim($validated['notes']) : null,
                'use_lid' => true,
            ]);

            foreach ($preparedItems as $itemData) {
                $order->items()->create($itemData);
            }

            // Potong Stok Otomatis langsung untuk Cup 16oz dan Lid (Tutup Cup)
            $this->deductCupAndLidStock($totalCups);

            $order->load('items');

            $cupStock = Ingredient::where('name', 'like', '%Cup%')
                ->where('name', 'not like', 'Lid%')
                ->first();
            $lidStock = Ingredient::where('name', 'like', '%Lid%')->first();

            return response()->json([
                'success' => true,
                'message' => "Transaksi disimpan! Stok Cup & Lid otomatis berkurang ({$totalCups} pcs).",
                'order' => $order,
                'cup_stock' => $cupStock ? $cupStock->current_stock : null,
                'lid_stock' => $lidStock ? $lidStock->current_stock : null,
            ], 201);
        });
    }

    /**
     * Kurangi stok Cup 16oz dan Lid (Tutup Cup) secara otomatis setiap ada pembelian.
     */
    private function deductCupAndLidStock(int $totalCups): void
    {
        if ($totalCups <= 0) {
            return;
        }

        // 1. Kurangi stok Cup 16oz
        $cupIngredient = Ingredient::where('name', 'like', '%Cup%')
            ->where('name', 'not like', 'Lid%')
            ->first();

        if ($cupIngredient) {
            $cupIngredient->update([
                'current_stock' => max(0, (int) $cupIngredient->current_stock - $totalCups),
            ]);
        }

        // 2. Kurangi stok Lid (Tutup Cup) langsung setiap pembelian
        $lidIngredient = Ingredient::where('name', 'like', '%Lid%')->first();

        if ($lidIngredient) {
            $lidIngredient->update([
                'current_stock' => max(0, (int) $lidIngredient->current_stock - $totalCups),
            ]);
        }
    }

    /**
     * Ubah status antrean pesanan (masuk -> dibuat -> selesai) dengan 1 klik.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:masuk,dibuat,selesai'],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        $order->load('items');

        return response()->json([
            'success' => true,
            'message' => "Status pesanan #{$order->order_number} diperbarui menjadi " . strtoupper($order->status) . '.',
            'order' => $order,
        ]);
    }
}
