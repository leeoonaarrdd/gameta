<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MemberOTP extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'member_otps';

    protected $fillable = [
        'member_id',
        'otp_code',
        'phone',
        'type',
        'status',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Relasi dengan Member
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Cek apakah OTP sudah expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Cek apakah OTP masih valid
     */
    public function isValid()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Mark OTP sebagai verified
     */
    public function markAsVerified()
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    /**
     * Mark OTP sebagai expired
     */
    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * Generate OTP code baru
     */
    public static function generateOTP()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Buat OTP baru untuk member
     */
    public static function createOTP($memberId, $phone, $type = 'verification')
    {
        // Expire OTP lama yang masih pending
        self::where('member_id', $memberId)
            ->where('type', $type)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Buat OTP baru
        return self::create([
            'member_id' => $memberId,
            'otp_code' => self::generateOTP(),
            'phone' => $phone,
            'type' => $type,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addMinutes(10), // OTP berlaku 10 menit
        ]);
    }

    /**
     * Verifikasi OTP
     */
    public static function verifyOTP($memberId, $otpCode, $type = 'verification')
    {
        $otp = self::where('member_id', $memberId)
            ->where('otp_code', $otpCode)
            ->where('type', $type)
            ->where('status', 'pending')
            ->first();

        if (!$otp) {
            return false;
        }

        if ($otp->isExpired()) {
            $otp->markAsExpired();
            return false;
        }

        $otp->markAsVerified();
        return true;
    }
}
