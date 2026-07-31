<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add staff-specific fields
            $table->enum('staff_role', ['order_manager', 'product_manager', 'all'])
                ->nullable()
                ->after('role')
                ->comment('Staff role: order_manager, product_manager, or all');
            
            $table->timestamp('staff_assigned_at')
                ->nullable()
                ->after('staff_role')
                ->comment('When user was assigned as staff');
            
            $table->timestamp('staff_deactivated_at')
                ->nullable()
                ->after('staff_assigned_at')
                ->comment('When staff was deactivated');
            
            $table->boolean('is_staff')
                ->default(false)
                ->after('staff_deactivated_at')
                ->index()
                ->comment('Quick flag for staff status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['staff_role', 'staff_assigned_at', 'staff_deactivated_at', 'is_staff']);
        });
    }
};
