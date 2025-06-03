<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Pierre',
            'email' => 'pierre@test.com',
        ]);
        User::factory()->create([
            'name' => 'Erwan',
            'email' => 'erwan@test.com',
        ]);User::factory()->create([
            'name' => 'Baptiste',
            'email' => 'baptiste@test.com',
        ]);
        User::factory()->create([
            'name' => 'Arnaud',
            'email' => 'arnaud@test.com',
        ]);
    }
}
