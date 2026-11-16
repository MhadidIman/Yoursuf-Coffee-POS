<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_type',
        'customer_name',
        'customer_wa',
        'delivery_area',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'cash_received',
        'change_amount',
        'status',
        'notes',
        'use_lid',
    ];

    protected function casts(): array
    {
        return [
            'delivery_fee' => 'integer',
            'discount_amount' => 'integer',
            'total_amount' => 'integer',
            'cash_received' => 'integer',
            'change_amount' => 'integer',
            'use_lid' => 'boolean',
        ];
    }

    /**
     * Relasi satu pesanan memiliki banyak item pesanan.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate nomor pesanan unik (Contoh: YC-20261111-001).
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'YC-' . now()->format('Ymd') . '-';
        $latestOrder = self::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextSequence = 1;
        if ($latestOrder) {
            $lastSequence = (int) substr($latestOrder->order_number, -3);
            $nextSequence = $lastSequence + 1;
        }

        return $prefix . str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);
    }
}
