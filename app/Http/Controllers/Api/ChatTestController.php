<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgoraChatService;

class ChatTestController extends Controller
{
    protected $agoraChatService;

    public function __construct(AgoraChatService $agoraChatService)
    {
        $this->agoraChatService = $agoraChatService;
    }

    /**
     * Test all Agora Chat functionality
     */
    public function testAll()
    {
        $results = [];

        // Test 1: Check credentials
        $results['credentials'] = [
            'org_name' => config('agora_chat.org_name'),
            'app_name' => config('agora_chat.app_name'),
            'client_id' => config('agora_chat.client_id') ? 'Set' : 'Not Set',
            'client_secret' => config('agora_chat.client_secret') ? 'Set' : 'Not Set',
            'base_url' => config('agora_chat.base_url')
        ];

        // Test 2: Test connection
        $results['connection'] = $this->agoraChatService->testConnection();

        // Test 3: Check if any users exist in database
        $users = \App\Models\User::whereNotNull('agora_chat_username')->count();
        $results['database_users'] = [
            'count' => $users,
            'message' => $users > 0 ? 'Users found with Agora Chat usernames' : 'No users with Agora Chat usernames'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Agora Chat Test Results',
            'results' => $results,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get system information
     */
    public function systemInfo()
    {
        return response()->json([
            'success' => true,
            'system_info' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database_connection' => config('database.default'),
                'cache_driver' => config('cache.default'),
                'queue_connection' => config('queue.default'),
                'app_environment' => config('app.env'),
                'app_debug' => config('app.debug')
            ],
            'agora_config' => [
                'config_file_exists' => config('agora_chat.org_name') !== null,
                'all_credentials_set' => config('agora_chat.org_name') &&
                                       config('agora_chat.app_name') &&
                                       config('agora_chat.client_id') &&
                                       config('agora_chat.client_secret')
            ]
        ]);
    }
}
