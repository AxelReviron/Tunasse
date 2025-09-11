<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'lexa',
            'email' => 'contact@axel-reviron.fr',
            'password' => Hash::make('password'),
        ]);

        $testUser = User::factory()->create([
            'name' => 'test',
            'email' => 'test@test.fr',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('user');
        $testUser->assignRole('user');
    }
}
