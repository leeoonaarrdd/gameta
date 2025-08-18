<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'kategori',
        'image',
        'provider',
        'method_code',
        'admin_fee',
        'admin_fee_percentage',
        'has_unique_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_unique_code' => 'boolean',
        'admin_fee' => 'integer',
        'admin_fee_percentage' => 'integer',
    ];

    /**
     * Scope untuk metode pembayaran yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengurutkan berdasarkan nama
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Relasi dengan kategori
     */
    public function category()
    {
        return $this->belongsTo(PaymentMethodCategory::class, 'kategori', 'name');
    }
}
