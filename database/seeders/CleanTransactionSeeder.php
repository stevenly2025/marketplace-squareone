<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua data transaksi & laporan
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        DB::table('reviews')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('carts')->truncate();
        DB::table('chats')->truncate();
        DB::table('notifications')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('✅ Data transaksi berhasil dihapus! (Reviews, Orders, Carts, Chats, Notifications)');
    }
}