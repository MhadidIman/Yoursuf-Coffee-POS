<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'current_stock',
        'min_stock_alert',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'min_stock_alert' => 'integer',
        ];
    }

    /**
     * Cek apakah stok bahan saat ini berada di bawah atau sama dengan batas minimum alert.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock_alert;
    }
}

