<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // KOREKSI UTAMA: Ubah nama & email menjadi username & nomor_hp sesuai TrustPay
        return [
            'username' => fake()->userName(),
            'nomor_hp' => '08' . fake()->numerify('##########'), // Menghasilkan nomor HP simulasi Indonesia
            'password' => static::$password ??= Hash::make('Password123!'), // Sesuai regex password ketat kamu
            'role' => 'nasabah',
            'remember_token' => Str::random(10),
        ];
    }
}