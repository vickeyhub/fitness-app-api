<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed default accounts: super admin, admin, and customer (stored as user_type `user`).
     */
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'username' => 'superadmin',
                'password' => 'password',
                'user_type' => 'super_admin',
                'status' => '1',
                'email_verified_at' => now(),
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'username' => 'admin',
                'password' => 'password',
                'user_type' => 'admin',
                'status' => '1',
                'email_verified_at' => now(),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'first_name' => 'Sample',
                'last_name' => 'Customer',
                'username' => 'customer',
                'password' => 'password',
                'user_type' => 'user',
                'status' => '1',
                'email_verified_at' => now(),
            ]
        );

        foreach ([$superAdmin, $admin, $customer] as $user) {
            if (! $user->profile) {
                $user->profile()->create([]);
            }
        }
    }
}
