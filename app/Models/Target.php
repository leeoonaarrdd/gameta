<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_target',
        'teks_header',
        'konten',
        'sparator',
        'input_fields',
        'option_fields',
        'is_active',
    ];

    protected $casts = [
        'input_fields' => 'array',
        'option_fields' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the input fields count
     */
    public function getInputFieldsCountAttribute()
    {
        return $this->input_fields ? count($this->input_fields) : 0;
    }

    /**
     * Get the option fields count
     */
    public function getOptionFieldsCountAttribute()
    {
        return $this->option_fields ? count($this->option_fields) : 0;
    }

    /**
     * Get the total fields count
     */
    public function getTotalFieldsCountAttribute()
    {
        return $this->input_fields_count + $this->option_fields_count;
    }

    /**
     * Get the games for this target.
     */
    public function games()
    {
        return $this->hasMany(Game::class);
    }
} 