<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database. Membuat satu staff admin
     * untuk development lokal: staff@chleo.test / password.
     */
    public function run(): void
    {
        User::where('email', 'staff@chleo.test')->first()
            ?? User::factory()->create([
                'name' => 'Staff Chleo',
                'email' => 'staff@chleo.test',
                'password' => Hash::make('password'),
            ]);
    }
}
