<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Memanggil seeder satu per satu secara berurutan [BEST PRACTICE]
        $this->call([
            UserSeeder::class,
            EwalletAccountSeeder::class,
            BankAccountSeeder::class,
            ValasAccountSeeder::class,
            KursSeeder::class,
            TransaksiHistorySeeder::class,
            PusatBantuanSeeder::class,
        ]);
    }
}