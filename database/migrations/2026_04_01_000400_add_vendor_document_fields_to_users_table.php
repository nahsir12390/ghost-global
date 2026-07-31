<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_nin')->nullable()->after('verification_notes');
            $table->string('verification_email')->nullable()->after('verification_nin');
            $table->string('verification_phone')->nullable()->after('verification_email');
            $table->string('verification_id_front_path')->nullable()->after('verification_phone');
            $table->string('verification_id_back_path')->nullable()->after('verification_id_front_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_nin',
                'verification_email',
                'verification_phone',
                'verification_id_front_path',
                'verification_id_back_path',
            ]);
        });
    }
};
