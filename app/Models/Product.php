<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'cost_price',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'cost_price' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Otomatis set cost_price (HPP) sebesar 50% dari harga jual jika belum diisi.
     */
    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (is_null($product->cost_price) || $product->cost_price === 0) {
                $product->cost_price = (int) round($product->price * 0.5);
            }
        });
    }

    /**
     * Relasi produk milik satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi produk terdapat di banyak detail pesanan (order_items).
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
