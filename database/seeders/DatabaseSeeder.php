<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);

        // Seed an admin user
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@kiddosheaven.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
    }
}
