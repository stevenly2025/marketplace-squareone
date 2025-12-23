<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek kalau kolom address belum ada, baru kita buat
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('email');
            }

            // Cek kalau kolom city belum ada, baru kita buat
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('email'); // atau after address
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};