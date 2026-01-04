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
            ['email' => config('app.user.email')],
            [
                'name' => config('app.user.name'),
                'password' => Hash::make(config('app.user.password')),
            ]
        );
    }
}
