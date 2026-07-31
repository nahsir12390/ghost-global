<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate old statuses to new ones
        DB::table('orders')->where('status', 'pending')->update(['status' => 'ordered']);
        DB::table('orders')->where('status', 'processing')->update(['status' => 'confirmed']);
        DB::table('orders')->where('status', 'packed')->update(['status' => 'picked_up']);
        DB::table('orders')->where('status', 'shipped')->update(['status' => 'on_the_way']);
        // on_the_way stays on_the_way
        // delivered stays delivered
        // cancelled stays cancelled
        DB::table('orders')->where('status', 'failed')->update(['status' => 'cancelled']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old statuses
        DB::table('orders')->where('status', 'ordered')->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'confirmed')->update(['status' => 'processing']);
        DB::table('orders')->where('status', 'picked_up')->update(['status' => 'packed']);
        DB::table('orders')->where('status', 'on_the_way')->update(['status' => 'shipped']);
        DB::table('orders')->where('status', 'cancelled')->update(['status' => 'failed']);
    }
};
