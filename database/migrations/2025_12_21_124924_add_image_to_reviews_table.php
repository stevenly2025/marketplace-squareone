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
        Schema::table('reviews', function (Blueprint $table) {
            // Menambahkan kolom 'image' setelah kolom 'comment'
            // nullable() artinya user tidak wajib upload gambar
            $table->string('image')->nullable()->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('image');
        });
    }
};