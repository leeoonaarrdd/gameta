<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topup extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'topup_id',
        'jumlah',
        'status',
        'payment_method',
        'payment_account',
        'payment_name',
        'payment_code',
        'payment_provider',
        'payment_category',
        'payment_notes',
        'tripay_reference',
        'tripay_payment_url',
        'tripay_qr_code',
        'tripay_merchant_ref',
        'member_id',
        'tanggal'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'datetime'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
