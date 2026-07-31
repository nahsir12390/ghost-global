<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('is_admin');
            $table->string('store_name')->nullable()->after('role');
            $table->string('store_slug')->nullable()->unique()->after('store_name');
            $table->string('phone')->nullable()->after('store_slug');
            $table->text('address')->nullable()->after('phone');
        });

        DB::table('users')
            ->where('is_admin', true)
            ->update([
                'role' => 'admin',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['store_slug']);
            $table->dropColumn(['role', 'store_name', 'store_slug', 'phone', 'address']);
        });
    }
};
