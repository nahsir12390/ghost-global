<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type')->default('physical')->after('description');
            }

            if (! Schema::hasColumn('products', 'download_file_path')) {
                $table->string('download_file_path')->nullable()->after('images');
            }

            if (! Schema::hasColumn('products', 'download_link')) {
                $table->string('download_link')->nullable()->after('download_file_path');
            }

            if (! Schema::hasColumn('products', 'course_access_url')) {
                $table->string('course_access_url')->nullable()->after('download_link');
            }

            if (! Schema::hasColumn('products', 'access_instructions')) {
                $table->text('access_instructions')->nullable()->after('course_access_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('products', 'product_type') ? 'product_type' : null,
                Schema::hasColumn('products', 'download_file_path') ? 'download_file_path' : null,
                Schema::hasColumn('products', 'download_link') ? 'download_link' : null,
                Schema::hasColumn('products', 'course_access_url') ? 'course_access_url' : null,
                Schema::hasColumn('products', 'access_instructions') ? 'access_instructions' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
