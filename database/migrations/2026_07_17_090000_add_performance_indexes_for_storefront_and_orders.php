<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured'], 'products_active_featured_index');
            $table->index(['is_active', 'created_at'], 'products_active_created_at_index');
            $table->index(['is_active', 'category_id'], 'products_active_category_index');
            $table->index(['is_active', 'product_type'], 'products_active_type_index');
            $table->index(['is_active', 'price'], 'products_active_price_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active'], 'categories_active_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'orders_status_created_at_index');
            $table->index(['payment_status', 'created_at'], 'orders_payment_status_created_at_index');
        });

        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->index(['is_active'], 'newsletter_subscribers_active_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_featured_index');
            $table->dropIndex('products_active_created_at_index');
            $table->dropIndex('products_active_category_index');
            $table->dropIndex('products_active_type_index');
            $table->dropIndex('products_active_price_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_active_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_at_index');
            $table->dropIndex('orders_payment_status_created_at_index');
        });

        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropIndex('newsletter_subscribers_active_index');
        });
    }
};
