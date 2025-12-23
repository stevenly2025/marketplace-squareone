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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            
            // Path file gambar banner (disimpan di storage)
            $table->string('image'); 
            
            // Link tujuan jika banner diklik (Opsional/Nullable)
            $table->string('link')->nullable(); 
            
            // Status aktif (Default: true/aktif)
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus tabel jika rollback
        Schema::dropIfExists('banners');
    }
};