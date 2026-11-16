<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_owner_can_access_pos_and_admin_pages(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::where('email', 'owner@yoursufcoffee.com')->firstOrFail();

        $this->actingAs($owner)
            ->get('/')
            ->assertStatus(200)
            ->assertSee('YOURSUF COFFEE');

        $this->actingAs($owner)
            ->get('/admin')
            ->assertStatus(200)
            ->assertSee('BACKOFFICE');
    }

    public function test_pos_order_deducts_cup_and_lid_and_void_restores_them(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::where('email', 'owner@yoursufcoffee.com')->firstOrFail();
        $product = Product::where('name', 'Butterscotch Latte')->firstOrFail();

        $cupBefore = Ingredient::where('name', 'Cup 16oz')->value('current_stock');
        $lidBefore = Ingredient::where('name', 'Lid (Tutup Cup)')->value('current_stock');

        // Checkout 2 cup Butterscotch Latte (2 x 22000 = 44000) dengan diskon 4000 -> total 40000
        $response = $this->actingAs($owner)->postJson(route('pos.orders.store'), [
            'order_type' => 'delivery',
            'customer_name' => 'Budi UMY',
            'customer_wa' => '08123456789',
            'delivery_area' => 'UMY',
            'delivery_fee' => 0,
            'discount_amount' => 4000,
            'payment_method' => 'cash',
            'cash_received' => 50000,
            'notes' => 'Gedung AR B',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'item_notes' => 'Less Sugar',
                ],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.total_amount', 40000)
            ->assertJsonPath('order.discount_amount', 4000)
            ->assertJsonPath('order.change_amount', 10000)
            ->assertJsonPath('order.status', 'masuk');

        $this->assertEquals($cupBefore - 2, Ingredient::where('name', 'Cup 16oz')->value('current_stock'));
        $this->assertEquals($lidBefore - 2, Ingredient::where('name', 'Lid (Tutup Cup)')->value('current_stock'));

        $orderId = $response->json('order.id');

        // Geser status antrean: masuk -> dibuat -> selesai
        $this->actingAs($owner)
            ->patchJson("/pos/orders/{$orderId}/status", ['status' => 'dibuat'])
            ->assertOk()
            ->assertJsonPath('order.status', 'dibuat');

        $this->actingAs($owner)
            ->patchJson("/pos/orders/{$orderId}/status", ['status' => 'selesai'])
            ->assertOk()
            ->assertJsonPath('order.status', 'selesai');

        // Void / batalkan nota di Admin -> stok Cup & Lid otomatis kembali
        $this->actingAs($owner)
            ->deleteJson("/admin/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('orders', ['id' => $orderId]);
        $this->assertEquals($cupBefore, Ingredient::where('name', 'Cup 16oz')->value('current_stock'));
        $this->assertEquals($lidBefore, Ingredient::where('name', 'Lid (Tutup Cup)')->value('current_stock'));
    }
}
