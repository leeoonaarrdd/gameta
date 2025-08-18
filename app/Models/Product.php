<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'game_id',
        'icon_id',
        'price_tamu',
        'price_member',
        'original_price',
        'margin_tamu',
        'margin_member',
        'provider',
        'sku',
        'is_active',
        'auto_update_price',
        'last_price_update'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_tamu' => 'integer',
        'price_member' => 'integer',
        'original_price' => 'integer',
        'margin_tamu' => 'integer',
        'margin_member' => 'integer',
        'auto_update_price' => 'boolean',
        'last_price_update' => 'datetime'
    ];

    /**
     * Get the game that owns the product.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the icon that owns the product.
     */
    public function icon()
    {
        return $this->belongsTo(Icon::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
