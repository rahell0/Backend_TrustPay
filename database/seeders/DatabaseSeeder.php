<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KursSeeder::class,
            EwalletAccountSeeder::class,
            BankAccountSeeder::class,
            ValasAccountSeeder::class,
            TransaksiHistorySeeder::class,
            PusatBantuanSeeder::class,
        ]);
    }
}