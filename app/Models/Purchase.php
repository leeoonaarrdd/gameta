<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'member_id',
        'product_id',
        'quantity',
        'price',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'tripay_reference',
        'tripay_payment_url',
        'tripay_qr_code',
        'tripay_merchant_ref',
        'digiflazz_ref_id',
        'digiflazz_status',
        'digiflazz_message',
        'digiflazz_buyer_sku_code',
        'digiflazz_customer_no',
        'notes',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'price' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
            'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            'completed' => 'bg-green-500/20 text-green-400 border-green-500/30',
            'cancelled' => 'bg-red-500/20 text-red-400 border-red-500/30',
            'failed' => 'bg-gray-500/20 text-gray-400 border-gray-500/30'
        ];

        return $statuses[$this->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'failed' => 'Failed'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    public function getDisplayUsernameAttribute()
    {
        $notes = json_decode($this->notes, true) ?? [];
        $isMemberPrice = $notes['is_member_price'] ?? false;
        
        if ($isMemberPrice && $this->member) {
            return $this->member->username ?? '-';
        }
        
        return '-';
    }

    /**
     * Get masked order ID for display
     */
    public function getMaskedOrderIdAttribute()
    {
        $orderId = $this->order_id;
        $length = strlen($orderId);
        
        if ($length > 8) {
            $visible = substr($orderId, 0, 4);
            $hidden = str_repeat('*', $length - 8);
            $end = substr($orderId, -4);
            return $visible . $hidden . $end;
        } elseif ($length > 4) {
            // Untuk Order ID yang lebih pendek, tampilkan 2 karakter pertama dan terakhir
            $visible = substr($orderId, 0, 2);
            $hidden = str_repeat('*', $length - 4);
            $end = substr($orderId, -2);
            return $visible . $hidden . $end;
        }
        
        return $orderId;
    }
}
