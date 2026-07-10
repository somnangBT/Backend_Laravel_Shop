<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ១. បង្ខំឱ្យបង្កើត Table products នៅទីនេះតែម្តង ប្រសិនបមិនទាន់មាន
        if (!Schema::hasTable('products')) {
           Schema::create('products', function (Blueprint $table) {
    $table->id('pro_id');
    $table->string('pro_code')->unique();
    $table->string('pro_name');
    $table->unsignedBigInteger('category_id'); 
    $table->integer('qty')->default(0); 
    $table->decimal('price', 10, 2);
    
    // បន្ថែម Column នេះចូលទៅក្នុង Table របស់អ្នក
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

        // ២. ទើបមកបង្កើត Table order_items តាមក្រោយ
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('product_id'); 
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