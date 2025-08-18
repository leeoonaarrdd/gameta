<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengurutkan berdasarkan order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get active categories with cache
     */
    public static function getActiveCategories()
    {
        return Cache::remember('active_categories', 3600, function () {
            return self::where('is_active', true)
                ->orderBy('order')
                ->get();
        });
    }

    /**
     * Clear category cache when model is updated
     */
    protected static function booted()
    {
        static::saved(function ($category) {
            Cache::forget('active_categories');
        });

        static::deleted(function ($category) {
            Cache::forget('active_categories');
        });
    }
} 