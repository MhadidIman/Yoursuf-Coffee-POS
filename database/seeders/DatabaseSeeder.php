<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Kosongkan data transaksi (pemasukan) dan pengeluaran agar mulai bersih dari Rp 0
        Schema::disableForeignKeyConstraints();
        OrderItem::truncate();
        Order::truncate();
        Expense::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Buat 1 Akun User Default (Pemilik Yoursuf Coffee)
        User::updateOrCreate(
            ['email' => 'owner@yoursufcoffee.com'],
            [
                'name' => 'Pemilik Yoursuf Coffee',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Data Menu Asli "Yoursuf Coffee" berdasarkan Kategori
        $catalog = [
            'Milk Based Series' => [
                [
                    'name' => 'Butterscotch Latte',
                    'price' => 22000,
                    'description' => 'espresso, magic milk, and butterscotch syrup',
                ],
                [
                    'name' => 'Huzzelnutt Latte',
                    'price' => 22000,
                    'description' => 'espresso, magic milk, and huzzelnutt syrup',
                ],
                [
                    'name' => 'Pandan Latte',
                    'price' => 22000,
                    'description' => 'espresso, magic milk, and pandan syrup',
                ],
                [
                    'name' => 'Caramel Latte',
                    'price' => 22000,
                    'description' => 'espresso, magic milk, and caramel syrup',
                ],
                [
                    'name' => 'Salted Caramel Latte',
                    'price' => 22000,
                    'description' => 'espresso, magic milk, and salted caramel syrup',
                ],
                [
                    'name' => 'Palm Sugar Latte',
                    'price' => 22000,
                    'description' => 'espresso, magic milk, and palm sugar syrup',
                ],
                [
                    'name' => 'Vanilla Latte',
                    'price' => 22000,
                    'description' => 'espresso, magic milk, and vanilla syrup',
                ],
                [
                    'name' => 'Vanilla Marie Latte',
                    'price' => 25000,
                    'description' => 'espresso, magic milk, vanilla syrup, and marie biscuit',
                ],
            ],
            'Non-Coffee' => [
                [
                    'name' => 'Matcha Latte',
                    'price' => 22000,
                    'description' => 'matcha, magic milk, & sweetened condensed milk',
                ],
                [
                    'name' => 'Red Velvet Latte',
                    'price' => 21000,
                    'description' => 'red velvet, magic milk, & sweetened condensed milk',
                ],
                [
                    'name' => 'Matcha Barry',
                    'price' => 25000,
                    'description' => 'matcha, magic milk, and barry syrup',
                ],
                [
                    'name' => 'Dark Chocolatte Latte',
                    'price' => 22000,
                    'description' => 'dark chocolatte, magic milk, & sweetened condensed milk',
                ],
                [
                    'name' => 'Taro Latte',
                    'price' => 22000,
                    'description' => 'Taro, magic milk, & sweetened condensed milk',
                ],
                [
                    'name' => 'Caramela',
                    'price' => 21000,
                    'description' => 'non-coffee caramel milk blend',
                ],
                [
                    'name' => 'Romansa Regal',
                    'price' => 21000,
                    'description' => 'magic milk and regal biscuit blend',
                ],
            ],
            'Mocktail Series' => [
                [
                    'name' => 'Lemonade',
                    'price' => 22000,
                    'description' => 'espresso, soda water, lemon syrup, and dried lemon',
                ],
                [
                    'name' => 'Sammint',
                    'price' => 22000,
                    'description' => 'espresso with secreat syrup',
                ],
                [
                    'name' => 'Smriwing',
                    'price' => 22000,
                    'description' => 'espresso, secreat syrup, and dried lemon',
                ],
            ],
            'Americano Series' => [
                [
                    'name' => 'Regular Americano',
                    'price' => 20000,
                    'description' => 'espresso and water',
                ],
            ],
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            foreach ($products as $item) {
                Product::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $item['name'],
                    ],
                    [
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'cost_price' => (int) round($item['price'] * 0.5),
                        'is_available' => true,
                    ]
                );
            }
        }

        // Bersihkan entri gabungan lama jika ada, agar Cup 16oz dan Lid terpisah
        Ingredient::where('name', 'Cup 16oz + Lid')->delete();

        // 3. Data Awal Stok Bahan Utama (Cup 16oz dan Lid di-reset ke 100 pcs)
        $ingredients = [
            [
                'name' => 'Cup 16oz',
                'unit' => 'pcs',
                'current_stock' => 100,
                'min_stock_alert' => 25,
            ],
            [
                'name' => 'Lid (Tutup Cup)',
                'unit' => 'pcs',
                'current_stock' => 100,
                'min_stock_alert' => 25,
            ],
            [
                'name' => 'Espresso Beans',
                'unit' => 'gr',
                'current_stock' => 1500,
                'min_stock_alert' => 300,
            ],
            [
                'name' => 'Magic Milk',
                'unit' => 'ml',
                'current_stock' => 6000,
                'min_stock_alert' => 1500,
            ],
            [
                'name' => 'Sweetened Condensed Milk',
                'unit' => 'gr',
                'current_stock' => 2000,
                'min_stock_alert' => 500,
            ],
            [
                'name' => 'Syrup Variants (Butterscotch, Caramel, Pandan, Vanilla)',
                'unit' => 'ml',
                'current_stock' => 2500,
                'min_stock_alert' => 500,
            ],
            [
                'name' => 'Non-Coffee Powder (Matcha, Red Velvet, Choco, Taro)',
                'unit' => 'gr',
                'current_stock' => 1000,
                'min_stock_alert' => 250,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::updateOrCreate(
                ['name' => $ingredient['name']],
                [
                    'unit' => $ingredient['unit'],
                    'current_stock' => $ingredient['current_stock'],
                    'min_stock_alert' => $ingredient['min_stock_alert'],
                ]
            );
        }
    }
}
