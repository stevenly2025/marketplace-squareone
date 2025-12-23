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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            
            // Relasi User (Pengirim dan Penerima)
            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->foreignId('receiver_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Konten Pesan
            $table->text('message');
            
            // Status Baca
            $table->boolean('is_read')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes untuk performa query
            $table->index(['sender_id', 'receiver_id']);
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};