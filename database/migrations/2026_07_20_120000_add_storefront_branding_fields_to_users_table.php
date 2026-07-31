<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('store_description')->nullable()->after('store_slug');
            $table->string('store_banner_path')->nullable()->after('store_description');
            $table->string('store_whatsapp')->nullable()->after('store_banner_path');
            $table->string('store_instagram')->nullable()->after('store_whatsapp');
            $table->string('store_facebook')->nullable()->after('store_instagram');
            $table->string('store_website')->nullable()->after('store_facebook');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'store_description',
                'store_banner_path',
                'store_whatsapp',
                'store_instagram',
                'store_facebook',
                'store_website',
            ]);
        });
    }
};
