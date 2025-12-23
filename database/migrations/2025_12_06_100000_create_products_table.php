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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('product_name', 50);
            $table->decimal('product_price', 10, 2);
            $table->integer('product_stock')->default(0);
            $table->enum('product_category', ['coffee', 'main-dish', 'drinks', 'desserts']);
            $table->string('product_image')->nullable();
            $table->timestamps();
            
            // Add foreign key constraint if coffee_shop_admin table exists
            // Note: Using admin_id as the foreign key since coffee_shop_admin table uses admin_id as primary key
            $table->foreign('admin_id')->references('admin_id')->on('coffee_shop_admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
