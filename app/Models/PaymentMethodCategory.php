<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'kategori', 'name');
    }
}
