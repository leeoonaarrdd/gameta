<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RapidApiService
{
    private $apiKey;
    private $baseUrl;
    private $host;

    public function __construct()
    {
        $this->apiKey = config('services.rapidapi.key');
        $this->baseUrl = 'https://id-game-checker.p.rapidapi.com';
        $this->host = 'id-game-checker.p.rapidapi.com';
    }

    /**
     * Check Mobile Legends nickname by ID and server
     */
    public function checkMobileLegendsNickname($userId, $serverId)
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->host,
            ])->get($this->baseUrl . '/mobile-legends/' . $userId . '/' . $serverId);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if the API response indicates success
                if (isset($data['error']) && $data['error'] === false && isset($data['data']['username'])) {
                    return [
                        'success' => true,
                        'nickname' => $data['data']['username'],
                        'data' => $data['data'],
                        'message' => 'Nickname ditemukan'
                    ];
                } else {
                    return [
                        'success' => false,
                        'nickname' => null,
                        'message' => $data['msg'] ?? 'Nickname tidak ditemukan'
                    ];
                }
            }

            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Gagal menghubungi API server'
            ];

        } catch (\Exception $e) {
            Log::error('RapidAPI Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Terjadi kesalahan pada server API'
            ];
        }
    }

    /**
     * Check Free Fire nickname by User ID
     */
    public function checkFreeFireNickname($userId)
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->host,
            ])->get($this->baseUrl . '/free-fire/' . $userId);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if the API response indicates success
                if (isset($data['error']) && $data['error'] === false && isset($data['data']['username'])) {
                    return [
                        'success' => true,
                        'nickname' => $data['data']['username'],
                        'data' => $data['data'],
                        'message' => 'Nickname ditemukan'
                    ];
                } else {
                    return [
                        'success' => false,
                        'nickname' => null,
                        'message' => $data['msg'] ?? 'Nickname tidak ditemukan'
                    ];
                }
            }

            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Gagal menghubungi API server'
            ];

        } catch (\Exception $e) {
            Log::error('Free Fire API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Terjadi kesalahan pada server API'
            ];
        }
    }

    /**
     * Check PUBG Mobile nickname by User ID
     */
    public function checkPubgMobileNickname($userId)
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->host,
            ])->get($this->baseUrl . '/pubgm-global/' . $userId);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if the API response indicates success
                if (isset($data['error']) && $data['error'] === false && isset($data['data']['username'])) {
                    return [
                        'success' => true,
                        'nickname' => $data['data']['username'],
                        'data' => $data['data'],
                        'message' => 'Nickname ditemukan'
                    ];
                } else {
                    return [
                        'success' => false,
                        'nickname' => null,
                        'message' => $data['msg'] ?? 'Nickname tidak ditemukan'
                    ];
                }
            }

            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Gagal menghubungi API server'
            ];

        } catch (\Exception $e) {
            Log::error('PUBG Mobile API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Terjadi kesalahan pada server API'
            ];
        }
    }

    /**
     * Check Honor of Kings nickname by User ID
     */
    public function checkHonorOfKingsNickname($userId)
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->host,
            ])->get($this->baseUrl . '/honor-of-kings/' . $userId);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if the API response indicates success
                if (isset($data['error']) && $data['error'] === false && isset($data['data']['username'])) {
                    return [
                        'success' => true,
                        'nickname' => $data['data']['username'],
                        'data' => $data['data'],
                        'message' => 'Nickname ditemukan'
                    ];
                } else {
                    return [
                        'success' => false,
                        'nickname' => null,
                        'message' => $data['msg'] ?? 'Nickname tidak ditemukan'
                    ];
                }
            }

            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Gagal menghubungi API server'
            ];

        } catch (\Exception $e) {
            Log::error('Honor of Kings API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Terjadi kesalahan pada server API'
            ];
        }
    }



    /**
     * Check Magic Chess Go Go nickname by User ID and Server ID
     */
    public function checkMagicChessGoGoNickname($userId, $serverId)
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->host,
            ])->get($this->baseUrl . '/mcgg/' . $userId . '/' . $serverId);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if the API response indicates success
                if (isset($data['error']) && $data['error'] === false && isset($data['data']['username'])) {
                    return [
                        'success' => true,
                        'nickname' => $data['data']['username'],
                        'data' => $data['data'],
                        'message' => 'Nickname ditemukan'
                    ];
                } else {
                    return [
                        'success' => false,
                        'nickname' => null,
                        'message' => $data['msg'] ?? 'Nickname tidak ditemukan'
                    ];
                }
            }

            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Gagal menghubungi API server'
            ];

        } catch (\Exception $e) {
            Log::error('Magic Chess Go Go API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Terjadi kesalahan pada server API'
            ];
        }
    }

    /**
     * Check Genshin Impact nickname by User ID and Server Region
     */
    public function checkGenshinImpactNickname($userId, $serverRegion)
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->host,
            ])->get($this->baseUrl . '/genshin/' . $userId . '/' . $serverRegion);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if the API response indicates success
                if (isset($data['error']) && $data['error'] === false && isset($data['data']['username'])) {
                    return [
                        'success' => true,
                        'nickname' => $data['data']['username'],
                        'data' => $data['data'],
                        'message' => 'Nickname ditemukan'
                    ];
                } else {
                    return [
                        'success' => false,
                        'nickname' => null,
                        'message' => $data['msg'] ?? 'Nickname tidak ditemukan'
                    ];
                }
            }

            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Gagal menghubungi API server'
            ];

        } catch (\Exception $e) {
            Log::error('Genshin Impact API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'nickname' => null,
                'message' => 'Terjadi kesalahan pada server API'
            ];
        }
    }

    /**
     * Check nickname for any game type
     */
    public function checkGameNickname($gameName, $playerFields)
    {
        $gameName = strtolower($gameName);
        
        // Check if it's Mobile Legends
        if (strpos($gameName, 'mobile legends') !== false || strpos($gameName, 'ml') !== false) {
            if (count($playerFields) >= 2) {
                $userId = $playerFields[0];
                $serverId = $playerFields[1];
                
                return $this->checkMobileLegendsNickname($userId, $serverId);
            } else {
                return [
                    'success' => false,
                    'nickname' => null,
                    'message' => 'Mobile Legends memerlukan ID Player dan Server ID'
                ];
            }
        }
        
        // Check if it's Free Fire
        if (strpos($gameName, 'free fire') !== false || strpos($gameName, 'ff') !== false) {
            if (count($playerFields) >= 1) {
                $userId = $playerFields[0];
                return $this->checkFreeFireNickname($userId);
            } else {
                return [
                    'success' => false,
                    'nickname' => null,
                    'message' => 'Free Fire memerlukan User ID'
                ];
            }
        }
        
        // Check if it's PUBG Mobile
        if (strpos($gameName, 'pubg') !== false || strpos($gameName, 'pubg mobile') !== false || strpos($gameName, 'pubgm') !== false) {
            if (count($playerFields) >= 1) {
                $userId = $playerFields[0];
                return $this->checkPubgMobileNickname($userId);
            } else {
                return [
                    'success' => false,
                    'nickname' => null,
                    'message' => 'PUBG Mobile memerlukan User ID'
                ];
            }
        }
        
        // Check if it's Honor of Kings
        if (strpos($gameName, 'honor of kings') !== false || strpos($gameName, 'hok') !== false || strpos($gameName, 'honor') !== false) {
            if (count($playerFields) >= 1) {
                $userId = $playerFields[0];
                return $this->checkHonorOfKingsNickname($userId);
            } else {
                return [
                    'success' => false,
                    'nickname' => null,
                    'message' => 'Honor of Kings memerlukan User ID'
                ];
            }
        }
        
        // Check if it's Magic Chess Go Go
        if (strpos($gameName, 'magic chess') !== false || strpos($gameName, 'mcgg') !== false || strpos($gameName, 'magic chess go go') !== false) {
            if (count($playerFields) >= 2) {
                $userId = $playerFields[0];
                $serverId = $playerFields[1];
                return $this->checkMagicChessGoGoNickname($userId, $serverId);
            } else {
                return [
                    'success' => false,
                    'nickname' => null,
                    'message' => 'Magic Chess Go Go memerlukan ID Player dan Server ID'
                ];
            }
        }
        
        // Check if it's Genshin Impact
        if (strpos($gameName, 'genshin') !== false || strpos($gameName, 'genshin impact') !== false || strpos($gameName, 'gi') !== false) {
            if (count($playerFields) >= 2) {
                $userId = $playerFields[0];
                $serverRegion = $playerFields[1];
                
                // Map server region values to API format
                $serverMapping = [
                    'os_asia' => 'asia',
                    'os_euro' => 'europe', 
                    'os_usa' => 'america',
                    'os_cht' => 'china'
                ];
                
                // Convert option field value to API server value
                $apiServer = $serverMapping[$serverRegion] ?? $serverRegion;
                
                return $this->checkGenshinImpactNickname($userId, $apiServer);
            } else {
                return [
                    'success' => false,
                    'nickname' => null,
                    'message' => 'Genshin Impact memerlukan UID dan Server Region'
                ];
            }
        }
        
        return [
            'success' => false,
            'nickname' => null,
            'message' => 'Game ini belum didukung untuk cek nickname otomatis'
        ];
    }
}
