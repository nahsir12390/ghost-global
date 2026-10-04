<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->boolean('enabled')->default(false);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->unsignedSmallInteger('min_days');
            $table->unsignedSmallInteger('max_days');
            $table->boolean('state_required')->default(false);
            $table->boolean('postal_required')->default(false);
            $table->string('import_charges')->default('Contact us to confirm any import charges before ordering.');
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->json('delivery_countries')->nullable();
            $table->unsignedSmallInteger('processing_min_days')->default(0);
            $table->unsignedSmallInteger('processing_max_days')->default(0);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('buyer_email')->nullable();
            $table->string('buyer_whatsapp', 32)->nullable();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->boolean('marketing_consent')->default(false);
            $table->date('estimated_delivery_from')->nullable();
            $table->date('estimated_delivery_to')->nullable();
            $table->string('delivery_import_charges')->nullable();
        });
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_order_reference')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->string('purchase_currency', 3)->default('NGN');
            $table->text('private_notes')->nullable();
            $table->string('status')->default('preparing');
            $table->string('carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('tracking_url')->nullable();
            $table->date('estimated_from')->nullable();
            $table->date('estimated_to')->nullable();
            $table->text('customer_update')->nullable();
            $table->timestamps();
        });
        Schema::create('order_item_shipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->unique(['shipment_id', 'order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_shipment');
        Schema::dropIfExists('shipments');
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['buyer_email', 'buyer_whatsapp', 'terms_accepted_at', 'marketing_consent', 'estimated_delivery_from', 'estimated_delivery_to', 'delivery_import_charges']));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['delivery_countries', 'processing_min_days', 'processing_max_days']));
        Schema::dropIfExists('delivery_destinations');
    }
};
