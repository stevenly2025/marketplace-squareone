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
        // 1. Kategori Produk
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // 2. Produk SquareOne
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 15, 2);
            $table->integer('stock');
            $table->json('images'); // Menyimpan banyak foto produk
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        // 3. Keranjang (Grouping by Seller)
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('seller_id')->constrained('users');
            $table->integer('quantity');
            $table->decimal('price_snapshot', 15, 2);
            $table->timestamps();
        });
        // 4. Pesanan/Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Format: ORD-YYYYMMDD-XXXXX
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('seller_id')->constrained('users');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_proof')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->text('shipping_address');
            $table->string('shipping_phone');
            $table->string('resi_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};