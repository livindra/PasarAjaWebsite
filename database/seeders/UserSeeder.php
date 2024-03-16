<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'phone_number' => '6285655864624',
            'email' => 'hakiahmad756@gmail.com',
            'full_name' => 'Achmad Baihaqi',
            'password' => '$2y$12$74jkra2isCFBkJcnrY7Y2edY8xw/gkX.uuaP8CY.zF97HAMP8Glle',
            'pin' => '$2y$12$bdru8P9QGViFD3PDbqIUjuMRMoeeZdnqyG6rlPasQ/0UAAtAiChQy',
            'is_verified' => '1',
        ]);
    }
}
