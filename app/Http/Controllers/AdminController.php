<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Tampilkan halaman utama Admin Backoffice Yoursuf Coffee.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'finance');
        $activePeriod = $request->query('period', 'today');

        if (! in_array($activePeriod, ['today', 'week', 'month', 'custom'], true)) {
            $activePeriod = 'today';
        }

        $customStartDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $customEndDate = $request->query('end_date', now()->toDateString());

        try {
            $parsedStart = Carbon::parse($customStartDate)->startOfDay();
            $parsedEnd = Carbon::parse($customEndDate)->endOfDay();
            if ($parsedStart->gt($parsedEnd)) {
                [$parsedStart, $parsedEnd] = [$parsedEnd->copy()->startOfDay(), $parsedStart->copy()->endOfDay()];
            }
        } catch (\Throwable $e) {
            $parsedStart = now()->startOfMonth()->startOfDay();
            $parsedEnd = now()->endOfDay();
        }

        $customStartDate = $parsedStart->toDateString();
        $customEndDate = $parsedEnd->toDateString();

        // Hitung statistik keuangan untuk 4 opsi periode: Hari Ini, Minggu Ini, Bulan Ini, & Custom
        $financeStats = [
            'today' => $this->calculatePeriodStats(
                now()->startOfDay(),
                now()->endOfDay(),
                'Hari Ini (' . now()->translatedFormat('d M Y') . ')'
            ),
            'week' => $this->calculatePeriodStats(
                now()->startOfWeek(),
                now()->endOfWeek(),
                'Minggu Ini (' . now()->startOfWeek()->translatedFormat('d M') . ' - ' . now()->endOfWeek()->translatedFormat('d M Y') . ')'
            ),
            'month' => $this->calculatePeriodStats(
                now()->startOfMonth(),
                now()->endOfMonth(),
                'Bulan Ini (' . now()->translatedFormat('F Y') . ')'
            ),
            'custom' => $this->calculatePeriodStats(
                $parsedStart,
                $parsedEnd,
                'Rentang Tanggal (' . $parsedStart->translatedFormat('d M Y') . ' s/d ' . $parsedEnd->translatedFormat('d M Y') . ')'
            ),
        ];

        $categories = Category::orderBy('id')->get();

        $products = Product::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $expenses = Expense::orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        $ingredients = Ingredient::orderByRaw('CASE WHEN current_stock <= min_stock_alert THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->get();

        $orders = Order::with('items')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.index', compact(
            'activeTab',
            'activePeriod',
            'customStartDate',
            'customEndDate',
            'financeStats',
            'categories',
            'products',
            'expenses',
            'ingredients',
            'orders'
        ));
    }

    /**
     * Endpoint JSON untuk mengambil statistik keuangan berdasarkan Rentang Tanggal Custom.
     */
    public function getCustomFinanceStats(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->endOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $label = 'Rentang Tanggal (' . $start->translatedFormat('d M Y') . ' s/d ' . $end->translatedFormat('d M Y') . ')';
        $stats = $this->calculatePeriodStats($start, $end, $label);

        return response()->json([
            'success' => true,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'stats' => $stats,
        ]);
    }

    /**
     * Export Laporan Keuangan Lengkap ke file Excel (CSV UTF-8 dengan BOM).
     */
    public function exportFinanceCsv(Request $request): StreamedResponse
    {
        $period = $request->query('period', 'today');
        [$start, $end, $label, $fileSuffix] = $this->resolvePeriodRange(
            $period,
            $request->query('start_date'),
            $request->query('end_date')
        );

        $stats = $this->calculatePeriodStats($start, $end, $label);

        $orders = Order::with('items')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        $fileName = "Laporan-Keuangan-Yoursuf-Coffee-{$fileSuffix}.csv";

        return response()->streamDownload(function () use ($stats, $orders, $expenses, $start, $end) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar Microsoft Excel langsung mengenali karakter dan kolom dengan rapi
            fwrite($handle, "\xEF\xBB\xBF");

            // Header Utama Laporan
            fputcsv($handle, ['LAPORAN KEUANGAN & LABA BERSIH — YOURSUF COFFEE POS']);
            fputcsv($handle, ['Periode Laporan', $stats['label']]);
            fputcsv($handle, ['Rentang Tanggal', $start->format('Y-m-d') . ' s/d ' . $end->format('Y-m-d')]);
            fputcsv($handle, ['Dicetak Pada', now()->format('Y-m-d H:i:s') . ' WIB']);
            fputcsv($handle, []);

            // Bagian 1: Ringkasan Eksekutif Keuangan
            fputcsv($handle, ['=== 1. RINGKASAN KEUANGAN & LABA BERSIH ===']);
            fputcsv($handle, ['Metrik Keuangan', 'Nilai (Rp / Jumlah)', 'Keterangan']);
            fputcsv($handle, ['Total Transaksi Penjualan', $stats['total_orders'], 'Nota transaksi berhasil']);
            fputcsv($handle, ['Total Volume Cup Terjual', $stats['total_cups_sold'], 'Total cup terjual']);
            fputcsv($handle, ['Total Omzet Penjualan (Rp)', $stats['total_omzet'], 'Pendapatan kotor masuk']);
            fputcsv($handle, ['Total Potongan Diskon Promo (Rp)', $stats['total_discount'], 'Akumulasi diskon bundling']);
            fputcsv($handle, ['Total Modal HPP Produk Terjual (Rp)', $stats['total_hpp'], 'Akumulasi HPP cup terjual']);
            fputcsv($handle, ['Estimasi Laba Kotor (Rp)', $stats['gross_profit'], 'Omzet Penjualan - Modal HPP']);
            fputcsv($handle, ['Total Pengeluaran Belanja Kos (Rp)', $stats['total_expenses'], 'Belanja bahan baku, kemasan & operasional']);
            fputcsv($handle, ['LABA BERSIH KAS (Rp)', $stats['net_cash_profit'], 'Total Omzet - Total Pengeluaran Belanja']);
            fputcsv($handle, ['Uang Masuk CASH / Tunai (Rp)', $stats['total_cash'], $stats['cash_count'] . ' transaksi tunai']);
            fputcsv($handle, ['Uang Masuk QRIS (Rp)', $stats['total_qris'], $stats['qris_count'] . ' transaksi QRIS']);
            fputcsv($handle, []);

            // Bagian 2: Daftar 5 Menu Terlaris
            fputcsv($handle, ['=== 2. DAFTAR 5 MENU TERLARIS ===']);
            fputcsv($handle, ['Peringkat', 'Nama Menu', 'Jumlah Terjual (Cup)', 'Total Omzet (Rp)', 'Estimasi Laba Kotor (Rp)']);
            if (! empty($stats['top_products'])) {
                foreach ($stats['top_products'] as $idx => $top) {
                    fputcsv($handle, [
                        '#' . ($idx + 1),
                        $top['product_name'],
                        $top['total_qty'],
                        $top['total_revenue'],
                        $top['total_profit'],
                    ]);
                }
            } else {
                fputcsv($handle, ['-', 'Belum ada penjualan pada periode ini', 0, 0, 0]);
            }
            fputcsv($handle, []);

            // Bagian 3: Rincian Transaksi Penjualan
            fputcsv($handle, ['=== 3. RINCIAN TRANSAKSI PENJUALAN ===']);
            fputcsv($handle, [
                'No. Order',
                'Tanggal & Waktu',
                'Nama Pelanggan',
                'No. WA',
                'Tipe Pesanan',
                'Area Pengantaran',
                'Rincian Menu & Qty',
                'Jumlah Cup',
                'Ongkir (Rp)',
                'Diskon (Rp)',
                'Total Bayar (Rp)',
                'Metode Pembayaran',
                'Status Pesanan',
            ]);

            foreach ($orders as $ord) {
                $itemSummary = $ord->items->map(function ($item) {
                    $note = $item->item_notes ? " ({$item->item_notes})" : '';
                    return "{$item->quantity}x {$item->product_name}{$note}";
                })->implode(' | ');

                $cupQty = (int) $ord->items->sum('quantity');

                fputcsv($handle, [
                    $ord->order_number,
                    optional($ord->created_at)->format('Y-m-d H:i'),
                    $ord->customer_name ?: 'Pelanggan Umum',
                    $ord->customer_wa ?: '-',
                    strtoupper($ord->order_type),
                    $ord->order_type === 'delivery' ? ($ord->delivery_area ?: '-') : 'Takeaway Kos',
                    $itemSummary,
                    $cupQty,
                    $ord->delivery_fee,
                    $ord->discount_amount,
                    $ord->total_amount,
                    strtoupper($ord->payment_method),
                    strtoupper($ord->status),
                ]);
            }
            fputcsv($handle, []);

            // Bagian 4: Rincian Pengeluaran Belanja (Expense Tracker)
            fputcsv($handle, ['=== 4. RINCIAN PENGELUARAN BELANJA USAHA ===']);
            fputcsv($handle, ['Tanggal Belanja', 'Nama Pengeluaran', 'Kategori', 'Nominal (Rp)', 'Catatan']);
            foreach ($expenses as $exp) {
                fputcsv($handle, [
                    optional($exp->expense_date)->format('Y-m-d'),
                    $exp->title,
                    $exp->category,
                    $exp->amount,
                    $exp->notes ?: '-',
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Tentukan objek Carbon start, end, label, dan nama file berdasarkan periode.
     */
    private function resolvePeriodRange(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        return match ($period) {
            'week' => [
                now()->startOfWeek(),
                now()->endOfWeek(),
                'Minggu Ini (' . now()->startOfWeek()->translatedFormat('d M') . ' - ' . now()->endOfWeek()->translatedFormat('d M Y') . ')',
                'Minggu-Ini-' . now()->startOfWeek()->format('Ymd') . '-' . now()->endOfWeek()->format('Ymd'),
            ],
            'month' => [
                now()->startOfMonth(),
                now()->endOfMonth(),
                'Bulan Ini (' . now()->translatedFormat('F Y') . ')',
                'Bulan-' . now()->format('Y-m'),
            ],
            'custom' => (function () use ($startDate, $endDate) {
                try {
                    $s = Carbon::parse($startDate ?: now()->startOfMonth()->toDateString())->startOfDay();
                    $e = Carbon::parse($endDate ?: now()->toDateString())->endOfDay();
                    if ($s->gt($e)) {
                        [$s, $e] = [$e->copy()->startOfDay(), $s->copy()->endOfDay()];
                    }
                } catch (\Throwable $ex) {
                    $s = now()->startOfMonth()->startOfDay();
                    $e = now()->endOfDay();
                }

                return [
                    $s,
                    $e,
                    'Rentang Tanggal (' . $s->translatedFormat('d M Y') . ' s/d ' . $e->translatedFormat('d M Y') . ')',
                    'Custom-' . $s->format('Ymd') . '-sd-' . $e->format('Ymd'),
                ];
            })(),
            default => [
                now()->startOfDay(),
                now()->endOfDay(),
                'Hari Ini (' . now()->translatedFormat('d M Y') . ')',
                'Hari-Ini-' . now()->format('Y-m-d'),
            ],
        };
    }

    /**
     * Kalkulasi metrik keuangan berdasarkan rentang waktu.
     */
    private function calculatePeriodStats(Carbon $start, Carbon $end, string $label): array
    {
        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);

        $totalOmzet = (int) (clone $ordersQuery)->sum('total_amount');
        $totalDiscount = (int) (clone $ordersQuery)->sum('discount_amount');
        $totalOrders = (int) (clone $ordersQuery)->count();
        $totalCash = (int) (clone $ordersQuery)->where('payment_method', 'cash')->sum('total_amount');
        $totalQris = (int) (clone $ordersQuery)->where('payment_method', 'qris')->sum('total_amount');
        $cashCount = (int) (clone $ordersQuery)->where('payment_method', 'cash')->count();
        $qrisCount = (int) (clone $ordersQuery)->where('payment_method', 'qris')->count();

        $orderIds = (clone $ordersQuery)->pluck('id');

        $totalCupsSold = 0;
        $totalHpp = 0;
        $topProducts = [];

        if ($orderIds->isNotEmpty()) {
            $totalCupsSold = (int) OrderItem::whereIn('order_id', $orderIds)->sum('quantity');

            $totalHpp = (int) OrderItem::whereIn('order_id', $orderIds)
                ->selectRaw('COALESCE(SUM(cost_price * quantity), 0) as total_hpp')
                ->value('total_hpp');

            $topProducts = OrderItem::whereIn('order_id', $orderIds)
                ->select(
                    'product_name',
                    DB::raw('SUM(quantity) as total_qty'),
                    DB::raw('SUM(subtotal) as total_revenue'),
                    DB::raw('SUM((price - cost_price) * quantity) as total_profit')
                )
                ->groupBy('product_name')
                ->orderByDesc('total_qty')
                ->orderByDesc('total_revenue')
                ->limit(5)
                ->get()
                ->toArray();
        }

        $totalExpenses = (int) Expense::whereBetween('expense_date', [
            $start->toDateString(),
            $end->toDateString(),
        ])->sum('amount');

        $grossProfit = $totalOmzet - $totalHpp;
        $netCashProfit = $totalOmzet - $totalExpenses;

        return [
            'label' => $label,
            'total_omzet' => $totalOmzet,
            'total_discount' => $totalDiscount,
            'total_expenses' => $totalExpenses,
            'total_hpp' => $totalHpp,
            'gross_profit' => $grossProfit,
            'net_cash_profit' => $netCashProfit,
            'total_cash' => $totalCash,
            'total_qris' => $totalQris,
            'cash_count' => $cashCount,
            'qris_count' => $qrisCount,
            'total_cups_sold' => $totalCupsSold,
            'total_orders' => $totalOrders,
            'top_products' => $topProducts,
        ];
    }

    // =========================================================================
    // 2. PENCATATAN PENGELUARAN (EXPENSE TRACKER)
    // =========================================================================

    /**
     * Simpan data pengeluaran belanja bahan baru.
     */
    public function storeExpense(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:1'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $expense = Expense::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan pengeluaran berhasil disimpan.',
                'expense' => $expense,
            ], 201);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'expenses'])
            ->with('success', 'Catatan pengeluaran berhasil ditambahkan.');
    }

    /**
     * Perbarui data pengeluaran belanja bahan.
     */
    public function updateExpense(Request $request, Expense $expense): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:1'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $expense->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pengeluaran berhasil diperbarui.',
                'expense' => $expense,
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'expenses'])
            ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    /**
     * Hapus data pengeluaran belanja.
     */
    public function destroyExpense(Request $request, Expense $expense): RedirectResponse|JsonResponse
    {
        $expense->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan pengeluaran berhasil dihapus.',
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'expenses'])
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }

    // =========================================================================
    // 3. MANAJEMEN MENU & HPP (PRODUCTS)
    // =========================================================================

    /**
     * Tambah produk / menu kopi baru.
     */
    public function storeProduct(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0'],
            'cost_price' => ['nullable', 'integer', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $costPrice = isset($validated['cost_price']) && $validated['cost_price'] > 0
            ? (int) $validated['cost_price']
            : (int) round($validated['price'] * 0.5);

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => (int) $validated['price'],
            'cost_price' => $costPrice,
            'is_available' => $request->boolean('is_available', true),
        ]);

        $product->load('category');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Menu {$product->name} berhasil ditambahkan.",
                'product' => $product,
            ], 201);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'products'])
            ->with('success', "Menu {$product->name} berhasil ditambahkan.");
    }

    /**
     * Perbarui data menu, harga jual, dan HPP per cup.
     */
    public function updateProduct(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0'],
            'cost_price' => ['required', 'integer', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => (int) $validated['price'],
            'cost_price' => (int) $validated['cost_price'],
            'is_available' => $request->has('is_available')
                ? $request->boolean('is_available')
                : $product->is_available,
        ]);

        $product->load('category');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Menu {$product->name} berhasil diperbarui.",
                'product' => $product,
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'products'])
            ->with('success', "Menu {$product->name} berhasil diperbarui.");
    }

    /**
     * Toggle cepat status menu Tersedia / Habis secara instan.
     */
    public function toggleProductAvailability(Product $product): JsonResponse
    {
        $product->update([
            'is_available' => ! $product->is_available,
        ]);

        $product->load('category');

        $statusLabel = $product->is_available ? 'TERSEDIA' : 'HABIS';

        return response()->json([
            'success' => true,
            'message' => "Status menu {$product->name} diubah menjadi {$statusLabel}.",
            'product' => $product,
        ]);
    }

    /**
     * Hapus menu dari katalog.
     */
    public function destroyProduct(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $name = $product->name;
        $product->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Menu {$name} berhasil dihapus.",
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'products'])
            ->with('success', "Menu {$name} berhasil dihapus.");
    }

    // =========================================================================
    // 4. PENGINGAT STOK BAHAN UTAMA (INGREDIENTS)
    // =========================================================================

    /**
     * Tambah bahan baku baru ke daftar pemantauan stok kos.
     */
    public function storeIngredient(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'unit' => ['required', 'string', 'max:20'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
        ]);

        $ingredient = Ingredient::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bahan {$ingredient->name} berhasil ditambahkan.",
                'ingredient' => $ingredient,
            ], 201);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'ingredients'])
            ->with('success', "Bahan {$ingredient->name} berhasil ditambahkan.");
    }

    /**
     * Perbarui data stok bahan utama atau batas minimum alert.
     */
    public function updateIngredient(Request $request, Ingredient $ingredient): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'unit' => ['required', 'string', 'max:20'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
        ]);

        $ingredient->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stok bahan {$ingredient->name} berhasil diperbarui.",
                'ingredient' => $ingredient,
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'ingredients'])
            ->with('success', "Stok bahan {$ingredient->name} berhasil diperbarui.");
    }

    /**
     * Hapus item bahan baku dari daftar pengingat.
     */
    public function destroyIngredient(Request $request, Ingredient $ingredient): RedirectResponse|JsonResponse
    {
        $name = $ingredient->name;
        $ingredient->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bahan {$name} berhasil dihapus.",
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'ingredients'])
            ->with('success', "Bahan {$name} berhasil dihapus.");
    }

    // =========================================================================
    // 5. RIWAYAT & PEMBATALAN TRANSAKSI (VOID ORDER)
    // =========================================================================

    /**
     * Batalkan / Hapus (Void) transaksi pesanan dan kembalikan stok Cup & Lid secara otomatis.
     */
    public function destroyOrder(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $orderNumber = $order->order_number;

        DB::transaction(function () use ($order) {
            $totalCups = (int) $order->items()->sum('quantity');

            if ($totalCups > 0) {
                // Kembalikan stok Cup 16oz
                $cupIngredient = Ingredient::where('name', 'like', '%Cup%')
                    ->where('name', 'not like', 'Lid%')
                    ->first();

                if ($cupIngredient) {
                    $cupIngredient->increment('current_stock', $totalCups);
                }

                // Kembalikan stok Lid (Tutup Cup)
                $lidIngredient = Ingredient::where('name', 'like', '%Lid%')->first();
                if ($lidIngredient) {
                    $lidIngredient->increment('current_stock', $totalCups);
                }
            }

            $order->delete();
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Transaksi {$orderNumber} berhasil dibatalkan (Void) & stok Cup + Lid telah dikembalikan.",
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'orders'])
            ->with('success', "Transaksi {$orderNumber} berhasil dibatalkan (Void) dan stok Cup + Lid telah dikembalikan.");
    }
}
