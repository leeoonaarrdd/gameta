<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'order',
        'category_id',
        'target_id',
        'sub_judul',
        'logo',
        'gambar',
        'banner'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

    /**
     * Scope untuk game aktif
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
     * Get active games with cache
     */
    public static function getActiveGames()
    {
        return Cache::remember('active_games', 3600, function () {
            return self::where('is_active', true)
                ->orderBy('order')
                ->get();
        });
    }

    /**
     * Get game by slug with cache
     */
    public static function getBySlug($slug)
    {
        return Cache::remember("game_slug_{$slug}", 3600, function () use ($slug) {
            return self::where('slug', $slug)
                ->where('is_active', true)
                ->with(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        });
    }

    /**
     * Clear game cache when model is updated
     */
    protected static function booted()
    {
        static::saved(function ($game) {
            Cache::forget('active_games');
            Cache::forget("game_slug_{$game->slug}");
        });

        static::deleted(function ($game) {
            Cache::forget('active_games');
            Cache::forget("game_slug_{$game->slug}");
        });
    }
} 