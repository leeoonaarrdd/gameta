<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'kategori',
        'pertanyaan',
        'konten',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
