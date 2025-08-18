<?php

namespace App\Helpers;

use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;

class FonnteHelper
{
    protected static $fonnteService;

    /**
     * Get FonnteService instance
     */
    protected static function getService()
    {
        if (!self::$fonnteService) {
            self::$fonnteService = new FonnteService();
        }
        return self::$fonnteService;
    }

    /**
     * Kirim pesan WhatsApp
     */
    public static function sendMessage($phone, $message, $delay = 0)
    {
        try {
            return self::getService()->sendMessage($phone, $message, $delay);
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendMessage error', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            return false;
        }
    }

    /**
     * Kirim pesan dengan media
     */
    public static function sendMedia($phone, $message, $mediaUrl, $delay = 0)
    {
        try {
            return self::getService()->sendMedia($phone, $message, $mediaUrl, $delay);
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendMedia error', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi order baru
     */
    public static function sendOrderNotification($orderId, $customerPhone, $orderData = [])
    {
        try {
            $message = "🎉 *Pesanan Baru Diterima!*\n\n";
            $message .= "Terima kasih telah melakukan pemesanan di website kami.\n\n";
            $message .= "📋 *Detail Pesanan:*\n";
            $message .= "Order ID: `#{$orderId}`\n";
            
            if (!empty($orderData)) {
                if (isset($orderData['total_amount'])) {
                    $message .= "Total: Rp " . number_format($orderData['total_amount'], 0, ',', '.') . "\n";
                }
                if (isset($orderData['payment_method'])) {
                    $message .= "Metode Pembayaran: {$orderData['payment_method']}\n";
                }
            }
            
            $message .= "\n📱 *Status:* Menunggu pembayaran\n\n";
            $message .= "Silakan lakukan pembayaran sesuai instruksi yang telah dikirim.\n";
            $message .= "Jika ada pertanyaan, silakan hubungi customer service kami.\n\n";
            $message .= "Terima kasih! 🙏";

            return self::sendMessage($customerPhone, $message);
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendOrderNotification error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'phone' => $customerPhone
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi status pembayaran
     */
    public static function sendPaymentNotification($orderId, $customerPhone, $status, $orderData = [])
    {
        try {
            $statusText = match($status) {
                'paid' => '✅ Pembayaran Berhasil',
                'failed' => '❌ Pembayaran Gagal',
                'expired' => '⏰ Pembayaran Kadaluarsa',
                'pending' => '⏳ Menunggu Pembayaran',
                default => '📊 Status Pembayaran Diperbarui'
            };

            $message = "📊 *Update Status Pembayaran*\n\n";
            $message .= "Order ID: `#{$orderId}`\n";
            $message .= "Status: {$statusText}\n\n";
            
            if (!empty($orderData)) {
                if (isset($orderData['total_amount'])) {
                    $message .= "Total: Rp " . number_format($orderData['total_amount'], 0, ',', '.') . "\n";
                }
                if (isset($orderData['payment_method'])) {
                    $message .= "Metode: {$orderData['payment_method']}\n";
                }
            }
            
            $message .= "\n";

            if ($status === 'paid') {
                $message .= "🎉 Pembayaran Anda telah berhasil diterima!\n";
                $message .= "Tim kami akan segera memproses pesanan Anda.\n";
                $message .= "Anda akan menerima update selanjutnya.\n";
            } elseif ($status === 'failed') {
                $message .= "😔 Pembayaran Anda gagal diproses.\n";
                $message .= "Silakan coba lagi atau hubungi customer service.\n";
                $message .= "Kami siap membantu Anda.\n";
            } elseif ($status === 'expired') {
                $message .= "⏰ Batas waktu pembayaran telah berakhir.\n";
                $message .= "Silakan lakukan pemesanan ulang.\n";
                $message .= "Terima kasih atas kesabaran Anda.\n";
            } elseif ($status === 'pending') {
                $message .= "⏳ Pembayaran Anda sedang diproses.\n";
                $message .= "Mohon tunggu beberapa saat.\n";
                $message .= "Anda akan menerima notifikasi selanjutnya.\n";
            }

            $message .= "\nTerima kasih! 🙏";

            return self::sendMessage($customerPhone, $message);
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendPaymentNotification error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'phone' => $customerPhone,
                'status' => $status
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi pengiriman produk
     */
    public static function sendDeliveryNotification($orderId, $customerPhone, $deliveryData = [])
    {
        try {
            $message = "📦 *Update Pengiriman Produk*\n\n";
            $message .= "Order ID: `#{$orderId}`\n";
            $message .= "Status: ✅ Produk telah dikirim\n\n";
            
            if (!empty($deliveryData)) {
                if (isset($deliveryData['product_name'])) {
                    $message .= "Produk: {$deliveryData['product_name']}\n";
                }
                if (isset($deliveryData['quantity'])) {
                    $message .= "Jumlah: {$deliveryData['quantity']}\n";
                }
                if (isset($deliveryData['delivery_time'])) {
                    $message .= "Waktu Pengiriman: {$deliveryData['delivery_time']}\n";
                }
            }
            
            $message .= "\n🎉 Produk digital Anda telah berhasil dikirim!\n";
            $message .= "Silakan cek email atau dashboard akun Anda.\n";
            $message .= "Jika ada masalah, silakan hubungi customer service.\n\n";
            $message .= "Terima kasih telah mempercayai kami! 🙏";

            return self::sendMessage($customerPhone, $message);
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendDeliveryNotification error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'phone' => $customerPhone
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi customer service
     */
    public static function sendCustomerServiceMessage($customerPhone, $message, $agentName = null)
    {
        try {
            $formattedMessage = "💬 *Customer Service*\n\n";
            
            if ($agentName) {
                $formattedMessage .= "Dari: {$agentName}\n\n";
            }
            
            $formattedMessage .= $message . "\n\n";
            $formattedMessage .= "Terima kasih telah menghubungi kami! 🙏";

            return self::sendMessage($customerPhone, $formattedMessage);
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendCustomerServiceMessage error', [
                'error' => $e->getMessage(),
                'phone' => $customerPhone
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi promo/promosi
     */
    public static function sendPromoNotification($customerPhone, $promoData = [])
    {
        try {
            $message = "🎉 *PROMO SPESIAL!*\n\n";
            
            if (!empty($promoData)) {
                if (isset($promoData['title'])) {
                    $message .= "*{$promoData['title']}*\n\n";
                }
                if (isset($promoData['description'])) {
                    $message .= "{$promoData['description']}\n\n";
                }
                if (isset($promoData['discount'])) {
                    $message .= "💥 Diskon: {$promoData['discount']}\n";
                }
                if (isset($promoData['valid_until'])) {
                    $message .= "⏰ Berlaku sampai: {$promoData['valid_until']}\n";
                }
                if (isset($promoData['code'])) {
                    $message .= "🎫 Kode Promo: `{$promoData['code']}`\n";
                }
            }
            
            $message .= "\nJangan lewatkan kesempatan ini!\n";
            $message .= "Segera belanja sekarang di website kami.\n\n";
            $message .= "Terima kasih! 🙏";

            return self::sendMessage($customerPhone, $message);
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendPromoNotification error', [
                'error' => $e->getMessage(),
                'phone' => $customerPhone
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi topup baru
     */
    public static function sendTopupNewNotification($memberPhone, $topupData = [])
    {
        try {
            $topupNewMessage = \App\Models\Configuration::getValue('whatsapp_topup_new', '');
            if (empty($topupNewMessage)) {
                Log::warning('Topup new message template is empty');
                return false;
            }

            $message = str_replace(
                ['#username#', '#topup_id#', '#method#', '#quantity#'],
                [
                    $topupData['username'] ?? 'User',
                    $topupData['topup_id'] ?? 'N/A',
                    $topupData['payment_method'] ?? 'N/A',
                    number_format($topupData['amount'] ?? 0, 0, ',', '.')
                ],
                $topupNewMessage
            );

            $result = self::sendMessage($memberPhone, $message);
            
            if ($result) {
                Log::info('Topup new notification sent successfully', [
                    'phone' => $memberPhone,
                    'topup_id' => $topupData['topup_id'] ?? 'N/A'
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendTopupNewNotification error', [
                'error' => $e->getMessage(),
                'phone' => $memberPhone,
                'topup_data' => $topupData
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi topup berhasil
     */
    public static function sendTopupSuccessNotification($memberPhone, $topupData = [])
    {
        try {
            $topupSuccessMessage = \App\Models\Configuration::getValue('whatsapp_topup_success', '');
            if (empty($topupSuccessMessage)) {
                Log::warning('Topup success message template is empty');
                return false;
            }

            $message = str_replace(
                ['#username#', '#topup_id#', '#quantity#', '#balance#'],
                [
                    $topupData['username'] ?? 'User',
                    $topupData['topup_id'] ?? 'N/A',
                    number_format($topupData['amount'] ?? 0, 0, ',', '.'),
                    number_format($topupData['balance'] ?? 0, 0, ',', '.')
                ],
                $topupSuccessMessage
            );

            $result = self::sendMessage($memberPhone, $message);
            
            if ($result) {
                Log::info('Topup success notification sent successfully', [
                    'phone' => $memberPhone,
                    'topup_id' => $topupData['topup_id'] ?? 'N/A',
                    'amount' => $topupData['amount'] ?? 0,
                    'balance' => $topupData['balance'] ?? 0
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('FonnteHelper sendTopupSuccessNotification error', [
                'error' => $e->getMessage(),
                'phone' => $memberPhone,
                'topup_data' => $topupData
            ]);
            return false;
        }
    }

    /**
     * Format nomor telepon
     */
    public static function formatPhone($phone)
    {
        try {
            return self::getService()->formatPhoneNumber($phone);
        } catch (\Exception $e) {
            Log::error('FonnteHelper formatPhone error', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            return $phone;
        }
    }

    /**
     * Cek apakah Fonnte sudah dikonfigurasi
     */
    public static function isConfigured()
    {
        try {
            $token = \App\Models\Configuration::getValue('fonnte_token', '');
            return !empty($token);
        } catch (\Exception $e) {
            return false;
        }
    }


}
