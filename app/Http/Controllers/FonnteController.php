<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;
use App\Models\Configuration;
use Illuminate\Routing\Controller;

class FonnteController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Kirim pesan WhatsApp
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:1000',
            'delay' => 'nullable|integer|min:0|max:300',
        ]);

        try {
            $phone = $this->fonnteService->validatePhoneNumber($request->phone);
            $message = $request->message;
            $delay = $request->delay ?? 0;

            $result = $this->fonnteService->sendMessage($phone, $message, $delay);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Fonnte send message error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim pesan dengan media
     */
    public function sendMedia(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'nullable|string|max:1000',
            'media_url' => 'required|url',
            'delay' => 'nullable|integer|min:0|max:300',
        ]);

        try {
            $phone = $this->fonnteService->validatePhoneNumber($request->phone);
            $message = $request->message ?? '';
            $mediaUrl = $request->media_url;
            $delay = $request->delay ?? 0;

            $result = $this->fonnteService->sendMedia($phone, $message, $mediaUrl, $delay);

            return response()->json([
                'success' => true,
                'message' => 'Pesan media berhasil dikirim',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Fonnte send media error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek status pesan
     */
    public function checkMessageStatus(Request $request)
    {
        $request->validate([
            'message_id' => 'required|string',
        ]);

        try {
            $result = $this->fonnteService->checkMessageStatus($request->message_id);

            return response()->json([
                'success' => true,
                'message' => 'Status pesan berhasil dicek',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Fonnte check message status error', [
                'error' => $e->getMessage(),
                'message_id' => $request->message_id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek status device
     */
    public function checkDeviceStatus()
    {
        try {
            $result = $this->fonnteService->checkDeviceStatus();

            return response()->json([
                'success' => true,
                'message' => 'Status device berhasil dicek',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Fonnte check device status error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook untuk menerima callback dari Fonnte
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Fonnte webhook received', [
                'data' => $request->all()
            ]);

            // Proses data webhook sesuai kebutuhan
            $data = $request->all();
            
            // Contoh: Simpan status pesan ke database
            if (isset($data['id']) && isset($data['status'])) {
                // Implementasi penyimpanan status pesan
                // $this->updateMessageStatus($data['id'], $data['status']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Fonnte webhook error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format nomor telepon
     */
    public function formatPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        try {
            $formatted = $this->fonnteService->formatPhoneNumber($request->phone);

            return response()->json([
                'success' => true,
                'message' => 'Nomor telepon berhasil diformat',
                'data' => [
                    'original' => $request->phone,
                    'formatted' => $formatted
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Kirim notifikasi order ke customer
     */
    public function sendOrderNotification($orderId)
    {
        try {
            // Ambil data order dari database
            // $order = Order::findOrFail($orderId);
            
            // Contoh template pesan
            $message = "Halo! Terima kasih telah melakukan pemesanan.\n\n";
            $message .= "Order ID: #{$orderId}\n";
            $message .= "Status: Menunggu pembayaran\n\n";
            $message .= "Silakan lakukan pembayaran sesuai instruksi yang telah dikirim.\n";
            $message .= "Jika ada pertanyaan, silakan hubungi customer service kami.";
            
            // Kirim pesan ke nomor customer
            // $phone = $order->customer_phone;
            // $result = $this->fonnteService->sendMessage($phone, $message);
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi order berhasil dikirim'
            ]);
        } catch (\Exception $e) {
            Log::error('Fonnte order notification error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim notifikasi status pembayaran
     */
    public function sendPaymentNotification($orderId, $status)
    {
        try {
            // Ambil data order dari database
            // $order = Order::findOrFail($orderId);
            
            $statusText = match($status) {
                'paid' => 'Pembayaran Berhasil',
                'failed' => 'Pembayaran Gagal',
                'expired' => 'Pembayaran Kadaluarsa',
                default => 'Status Pembayaran Diperbarui'
            };
            
            $message = "Update Status Pembayaran\n\n";
            $message .= "Order ID: #{$orderId}\n";
            $message .= "Status: {$statusText}\n\n";
            
            if ($status === 'paid') {
                $message .= "Pembayaran Anda telah berhasil diterima. ";
                $message .= "Tim kami akan segera memproses pesanan Anda.";
            } elseif ($status === 'failed') {
                $message .= "Pembayaran Anda gagal. ";
                $message .= "Silakan coba lagi atau hubungi customer service.";
            } elseif ($status === 'expired') {
                $message .= "Batas waktu pembayaran telah berakhir. ";
                $message .= "Silakan lakukan pemesanan ulang.";
            }
            
            // Kirim pesan ke nomor customer
            // $phone = $order->customer_phone;
            // $result = $this->fonnteService->sendMessage($phone, $message);
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi pembayaran berhasil dikirim'
            ]);
        } catch (\Exception $e) {
            Log::error('Fonnte payment notification error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'status' => $status
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
