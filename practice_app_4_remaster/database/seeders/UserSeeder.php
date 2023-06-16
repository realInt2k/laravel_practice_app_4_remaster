<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            DB::table('users')->insert([
                'name' => (random_int(1, 2) == 1 ? "mr. " : "ms. ") . Str::random(10),
                'email' => Str::random(10) . '@' . Str::random(5) . '.com.' . Str::random(2),
                'password' => Hash::make(Str::random(8)),
            ]);
        }
    }
}
