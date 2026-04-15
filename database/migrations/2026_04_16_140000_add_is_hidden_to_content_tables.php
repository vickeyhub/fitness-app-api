<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('user_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('comment');
        });

        Schema::table('statuses', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('caption');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });

        Schema::table('statuses', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });
    }
};
