<?php

namespace App\Services;

use GetStream\StreamChat\Client;
use Illuminate\Support\Facades\Log;

class GetStreamService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.stream.key'),
            config('services.stream.secret')
        );
    }

    /**
     * Register user in GetStream.io
     */
    public function registerUser($user)
    {
        try {
            $streamUserId = 'user-' . $user->id;

            $this->client->upsertUser([
                'id' => $streamUserId,
                'name' => $user->first_name . ' ' . $user->last_name,
                'image' => $user->profile->profile_image ?? null,
                'custom' => [
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'phone' => $user->phone ?? null,
                ]
            ]);

            Log::info('User registered in GetStream', [
                'user_id' => $user->id,
                'stream_user_id' => $streamUserId
            ]);

            return [
                'success' => true,
                'stream_user_id' => $streamUserId
            ];
        } catch (\Exception $e) {
            Log::error('Failed to register user in GetStream', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate token for user
     */
    public function generateToken($user)
    {
        try {
            $streamUserId = 'user-' . $user->id;

            // First register user if not exists
            $this->registerUser($user);

            $token = $this->client->createToken($streamUserId);

            Log::info('Token generated for user', [
                'user_id' => $user->id,
                'stream_user_id' => $streamUserId
            ]);

            return [
                'success' => true,
                'token' => $token,
                'stream_user_id' => $streamUserId
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate token', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create channel between users
     */
    public function createChannel($channelType, $channelId, $members, $creatorId)
    {
        try {
            $channel = $this->client->Channel($channelType, $channelId, [
                'name' => ucfirst($channelId),
                'members' => $members,
            ]);

            $channel->create($creatorId);

            Log::info('Channel created in GetStream', [
                'channel_id' => $channelId,
                'channel_type' => $channelType,
                'members' => $members
            ]);

            return [
                'success' => true,
                'channel_id' => $channelId,
                'channel_type' => $channelType,
                'members' => $members
            ];
        } catch (\Exception $e) {
            Log::error('Failed to create channel', [
                'channel_id' => $channelId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Register multiple users at once
     */
    public function registerMultipleUsers($users)
    {
        $results = [];

        foreach ($users as $user) {
            $results[] = [
                'user_id' => $user->id,
                'result' => $this->registerUser($user)
            ];
        }

        return $results;
    }

    /**
     * Check if user exists in GetStream
     */
    public function userExists($userId)
    {
        try {
            $streamUserId = 'user-' . $userId;

            // Try to get user from GetStream
            $response = $this->client->getUser($streamUserId);

            Log::info('User exists in GetStream', [
                'user_id' => $userId,
                'stream_user_id' => $streamUserId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::info('User does not exist in GetStream', [
                'user_id' => $userId,
                'stream_user_id' => 'user-' . $userId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Register user only if not exists
     */
    public function registerUserIfNotExists($user)
    {
        try {
            $streamUserId = 'user-' . $user->id;

            // Check if user already exists
            if ($this->userExists($user->id)) {
                Log::info('User already exists in GetStream, skipping registration', [
                    'user_id' => $user->id,
                    'stream_user_id' => $streamUserId
                ]);

                return [
                    'success' => true,
                    'stream_user_id' => $streamUserId,
                    'status' => 'already_exists'
                ];
            }

            // Register user if not exists
            $this->client->upsertUser([
                'id' => $streamUserId,
                'name' => $user->first_name . ' ' . $user->last_name,
                'image' => $user->profile->profile_image ?? null,
                'custom' => [
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'phone' => $user->phone ?? null,
                ]
            ]);

            Log::info('User registered in GetStream', [
                'user_id' => $user->id,
                'stream_user_id' => $streamUserId
            ]);

            return [
                'success' => true,
                'stream_user_id' => $streamUserId,
                'status' => 'newly_registered'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to register user in GetStream', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get registration status for multiple users
     */
    public function getUsersRegistrationStatus($users)
    {
        $results = [];

        foreach ($users as $user) {
            $exists = $this->userExists($user->id);
            $results[] = [
                'user_id' => $user->id,
                'stream_user_id' => 'user-' . $user->id,
                'exists_in_getstream' => $exists,
                'name' => $user->first_name . ' ' . $user->last_name
            ];
        }

        return $results;
    }
}
