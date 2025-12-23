<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if columns need to be renamed (using raw SQL for better compatibility)
        if (Schema::hasColumn('products', 'name') && !Schema::hasColumn('products', 'product_name')) {
            DB::statement('ALTER TABLE products CHANGE name product_name VARCHAR(50) NOT NULL');
        }
        if (Schema::hasColumn('products', 'price') && !Schema::hasColumn('products', 'product_price')) {
            DB::statement('ALTER TABLE products CHANGE price product_price DECIMAL(10,2) NOT NULL');
        }
        if (Schema::hasColumn('products', 'stock') && !Schema::hasColumn('products', 'product_stock')) {
            DB::statement('ALTER TABLE products CHANGE stock product_stock INT NOT NULL DEFAULT 0');
        }
        if (Schema::hasColumn('products', 'category') && !Schema::hasColumn('products', 'product_category')) {
            DB::statement("ALTER TABLE products CHANGE category product_category ENUM('coffee','main-dish','drinks','desserts') NOT NULL");
        }
        if (Schema::hasColumn('products', 'image') && !Schema::hasColumn('products', 'product_image')) {
            DB::statement('ALTER TABLE products CHANGE image product_image VARCHAR(255) NULL');
        }
        
        // Add admin_id column if it doesn't exist
        if (!Schema::hasColumn('products', 'admin_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('id');
            });
            
            // Add foreign key constraint separately if coffee_shop_admin table exists
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->foreign('admin_id')->references('admin_id')->on('coffee_shop_admin')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // If foreign key fails, just add the column without constraint
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the column renames
        if (Schema::hasColumn('products', 'product_name') && !Schema::hasColumn('products', 'name')) {
            DB::statement('ALTER TABLE products CHANGE product_name name VARCHAR(255) NOT NULL');
        }
        if (Schema::hasColumn('products', 'product_price') && !Schema::hasColumn('products', 'price')) {
            DB::statement('ALTER TABLE products CHANGE product_price price DECIMAL(10,2) NOT NULL');
        }
        if (Schema::hasColumn('products', 'product_stock') && !Schema::hasColumn('products', 'stock')) {
            DB::statement('ALTER TABLE products CHANGE product_stock stock INT NOT NULL DEFAULT 0');
        }
        if (Schema::hasColumn('products', 'product_category') && !Schema::hasColumn('products', 'category')) {
            DB::statement("ALTER TABLE products CHANGE product_category category ENUM('coffee','main-dish','drinks','desserts') NOT NULL");
        }
        if (Schema::hasColumn('products', 'product_image') && !Schema::hasColumn('products', 'image')) {
            DB::statement('ALTER TABLE products CHANGE product_image image VARCHAR(255) NULL');
        }
        
        // Drop admin_id if it exists
        if (Schema::hasColumn('products', 'admin_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            });
        }
    }
};
