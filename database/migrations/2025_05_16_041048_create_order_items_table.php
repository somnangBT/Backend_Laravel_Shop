<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ១. បង្កើត Table products
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id('pro_id');
                $table->string('pro_code')->unique();
                $table->string('pro_name');
                $table->unsignedBigInteger('category_id'); 
                $table->integer('qty')->default(0); 
                $table->decimal('price', 10, 2);
                $table->decimal('discounted_price', 10, 2)->nullable(); 
                $table->text('description')->nullable(); 
                $table->integer('discount')->default(0); 
                $table->string('image')->nullable(); 
                
                $table->foreign('category_id')
                      ->references('cat_id')
                      ->on('categories')
                      ->onDelete('cascade');

                $table->timestamps();
            });
        }

        // ២. បង្កើត Table order_items (បានបន្ថែម Columns ដែលខ្វះ)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // បន្ថែម Columns ទាំងនេះ
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id'); 
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            
            // តភ្ជាប់ Foreign Keys
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade'); // ពេលលុប Order វានឹងលុប Items នេះចោលដែរ

            $table->foreign('product_id')
                  ->references('pro_id')
                  ->on('products')
                  ->onDelete('restrict');
                  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('products');
    }
};