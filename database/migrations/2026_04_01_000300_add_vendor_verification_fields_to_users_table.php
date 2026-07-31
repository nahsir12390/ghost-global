<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_status')->nullable()->after('address');
            $table->timestamp('verification_submitted_at')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verification_submitted_at');
            $table->text('verification_notes')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'verification_submitted_at',
                'verified_at',
                'verification_notes',
            ]);
        });
    }
};
