<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, number, boolean, image, select
            $table->string('group'); // general, payment, shipping, email, social
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('options')->nullable(); // for select fields
            $table->integer('order')->default(0);
            $table->boolean('is_public')->default(false); // if setting can be accessed publicly
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};