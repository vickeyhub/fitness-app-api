<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('super_admin','admin','trainer','user','gym') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::table('users')->where('user_type', 'super_admin')->update(['user_type' => 'admin']);

        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin','trainer','user','gym') NULL DEFAULT NULL");
    }
};
