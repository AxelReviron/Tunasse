<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'contact@axel-reviron.fr'],
            [
                'name' => 'lexa',
                'password' => Hash::make(config('app.user-seed.password')),
            ]
        );
    }
}
