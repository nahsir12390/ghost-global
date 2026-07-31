<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('vendor_id')
                ->nullable()
                ->after('product_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Product::query()
            ->whereNotNull('vendor_id')
            ->select(['id', 'vendor_id'])
            ->chunkById(200, function ($products): void {
                foreach ($products as $product) {
                    OrderItem::query()
                        ->where('product_id', $product->id)
                        ->whereNull('vendor_id')
                        ->update(['vendor_id' => $product->vendor_id]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_id');
        });
    }
};
