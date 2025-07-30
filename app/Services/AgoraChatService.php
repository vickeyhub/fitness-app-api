<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AgoraChatService
{
    protected $baseUrl;
    protected $orgName;
    protected $appName;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('agora.chat.base_url');
        $this->orgName = config('agora.chat.org_name');
        $this->appName = config('agora.chat.app_name');
        $this->clientId = config('agora.chat.client_id');
        $this->clientSecret = config('agora.chat.client_secret');
    }

    /**
     * Get access token for Agora Chat
     */
    public function getAccessToken()
    {
        return Cache::remember('agora_chat_token', 3500, function () {
            $url = $this->baseUrl . '/' . $this->orgName . '/' . $this->appName . '/token';

            // Log configuration for debugging
            Log::info('Agora Chat token request', [
                'url' => $url,
                'org_name' => $this->orgName,
                'app_name' => $this->appName,
                'client_id' => $this->clientId,
                'client_secret_set' => !empty($this->clientSecret)
            ]);

            try {
                $response = Http::post($url, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Agora Chat token received successfully');
                    return $data['access_token'] ?? null;
                }

                Log::error('Agora Chat token request failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'url' => $url,
                    'request_data' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->clientId,
                        'client_secret' => '***hidden***'
                    ]
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('Agora Chat token request exception', [
                    'message' => $e->getMessage(),
                    'url' => $url
                ]);
                return null;
            }
        });
    }

    /**
     * Register a new user in Agora Chat
     */
    public function registerUser($userData)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $url = $this->baseUrl . '/' . $this->orgName . '/' . $this->appName . '/users';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post($url, [
                'username' => $userData['username'],
                'password' => $userData['password'],
                'nickname' => $userData['nickname'] ?? '',
                'avatar' => $userData['avatar'] ?? ''
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            // Check if user already exists
            if ($response->status() === 409) {
                return ['success' => true, 'message' => 'User already exists'];
            }

            Log::error('Agora Chat user registration failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return ['success' => false, 'message' => 'Registration failed'];
        } catch (\Exception $e) {
            Log::error('Agora Chat user registration exception', [
                'message' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get user information from Agora Chat
     */
    public function getUser($username)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $url = $this->baseUrl . '/' . $this->orgName . '/' . $this->appName . '/users/' . $username;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($url);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'message' => 'User not found'];
        } catch (\Exception $e) {
            Log::error('Agora Chat get user exception', [
                'message' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete user from Agora Chat
     */
    public function deleteUser($username)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $url = $this->baseUrl . '/' . $this->orgName . '/' . $this->appName . '/users/' . $username;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->delete($url);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'User deleted successfully'];
            }

            return ['success' => false, 'message' => 'Failed to delete user'];
        } catch (\Exception $e) {
            Log::error('Agora Chat delete user exception', [
                'message' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Test Agora Chat connection
     */
    public function testConnection()
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to get access token. Please check your credentials.',
                'credentials' => [
                    'org_name' => $this->orgName,
                    'app_name' => $this->appName,
                    'client_id' => $this->clientId ? 'Set' : 'Not Set',
                    'client_secret' => $this->clientSecret ? 'Set' : 'Not Set',
                    'base_url' => $this->baseUrl
                ]
            ];
        }

        return [
            'success' => true,
            'message' => 'Agora Chat connection successful',
            'token' => substr($token, 0, 20) . '...',
            'credentials' => [
                'org_name' => $this->orgName,
                'app_name' => $this->appName,
                'client_id' => $this->clientId ? 'Set' : 'Not Set',
                'client_secret' => $this->clientSecret ? 'Set' : 'Not Set',
                'base_url' => $this->baseUrl
            ]
        ];
    }
}
