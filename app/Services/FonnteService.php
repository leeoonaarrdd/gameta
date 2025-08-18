<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Configuration;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;
    protected $baseUrl = 'https://api.fonnte.com';

    public function __construct()
    {
        $this->token = Configuration::getValue('fonnte_token', '');
    }

    /**
     * Kirim pesan WhatsApp
     */
    public function sendMessage($phone, $message, $delay = 0)
    {
        if (empty($this->token)) {
            throw new \Exception('Token Fonnte belum dikonfigurasi');
        }

        try {
            // Pastikan pesan tidak kosong dan bersih
            $cleanMessage = trim($message);
            if (empty($cleanMessage)) {
                throw new \Exception('Pesan tidak boleh kosong');
            }
            
            $payload = [
                'target' => $phone,
                'message' => $cleanMessage,
                'delay' => $delay,
            ];
            
            Log::info('Fonnte API request', [
                'url' => $this->baseUrl . '/send',
                'payload' => $payload,
                'token_length' => strlen($this->token),
                'message_length' => strlen($cleanMessage)
            ]);
            
            // Gunakan cURL seperti di dokumentasi Fonnte
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->baseUrl . '/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($payload),
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->token,
                    'Content-Type: application/x-www-form-urlencoded',
                ],
            ]);
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            
            if ($err) {
                throw new \Exception('cURL Error: ' . $err);
            }
            
            $data = json_decode($response, true);

            if (isset($data['status']) && $data['status'] === true) {
                Log::info('Fonnte message sent successfully', [
                    'phone' => $phone,
                    'message' => $message,
                    'response' => $data
                ]);
                return $data;
            } else {
                Log::error('Fonnte message failed', [
                    'phone' => $phone,
                    'message' => $message,
                    'response' => $data
                ]);
                throw new \Exception($data['message'] ?? 'Gagal mengirim pesan WhatsApp');
            }
        } catch (\Exception $e) {
            Log::error('Fonnte service error', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            throw $e;
        }
    }

    /**
     * Kirim pesan dengan media (gambar/dokumen)
     */
    public function sendMedia($phone, $message, $mediaUrl, $delay = 0)
    {
        if (empty($this->token)) {
            throw new \Exception('Token Fonnte belum dikonfigurasi');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/send', [
                'target' => $phone,
                'message' => $message,
                'media' => $mediaUrl,
                'delay' => $delay,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                Log::info('Fonnte media message sent successfully', [
                    'phone' => $phone,
                    'message' => $message,
                    'media' => $mediaUrl,
                    'response' => $data
                ]);
                return $data;
            } else {
                Log::error('Fonnte media message failed', [
                    'phone' => $phone,
                    'message' => $message,
                    'media' => $mediaUrl,
                    'response' => $data
                ]);
                throw new \Exception($data['message'] ?? 'Gagal mengirim pesan media WhatsApp');
            }
        } catch (\Exception $e) {
            Log::error('Fonnte media service error', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            throw $e;
        }
    }



    /**
     * Cek status pesan
     */
    public function checkMessageStatus($messageId)
    {
        if (empty($this->token)) {
            throw new \Exception('Token Fonnte belum dikonfigurasi');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/status', [
                'id' => $messageId
            ]);

            $data = $response->json();

            if ($response->successful()) {
                Log::info('Fonnte message status checked', [
                    'message_id' => $messageId,
                    'response' => $data
                ]);
                return $data;
            } else {
                Log::error('Fonnte message status check failed', [
                    'message_id' => $messageId,
                    'response' => $data
                ]);
                throw new \Exception($data['message'] ?? 'Gagal mengecek status pesan');
            }
        } catch (\Exception $e) {
            Log::error('Fonnte message status error', [
                'error' => $e->getMessage(),
                'message_id' => $messageId
            ]);
            throw $e;
        }
    }

    /**
     * Format nomor telepon untuk Fonnte
     */
    public function formatPhoneNumber($phone)
    {
        // Hapus karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum ada kode negara, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        // Hapus 62 di depan untuk Fonnte (mereka akan menambahkannya otomatis)
        if (substr($phone, 0, 2) === '62') {
            $phone = substr($phone, 2);
        }
        
        return $phone;
    }

    /**
     * Validasi nomor telepon
     */
    public function validatePhoneNumber($phone)
    {
        $formatted = $this->formatPhoneNumber($phone);
        
        // Validasi format nomor Indonesia (tanpa 62 di depan)
        if (!preg_match('/^[0-9]{9,12}$/', $formatted)) {
            throw new \Exception('Format nomor telepon tidak valid');
        }
        
        return $formatted;
    }

    public function checkDeviceStatus()
    {
        if (empty($this->token)) {
            throw new \Exception('Token Fonnte belum dikonfigurasi');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/device');

            $data = $response->json();

            if ($response->successful()) {
                Log::info('Fonnte device status checked', [
                    'response' => $data
                ]);
                return $data;
            } else {
                Log::error('Fonnte device status check failed', [
                    'response' => $data
                ]);
                throw new \Exception($data['message'] ?? 'Gagal mengecek status device');
            }
        } catch (\Exception $e) {
            Log::error('Fonnte device status error', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
