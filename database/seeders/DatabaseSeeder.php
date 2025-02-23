<?php

namespace Database\Seeders;

use App\Models\DiningTable;
use App\Models\Menu;
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
            'name' => 'Test Admin',
            'email' => 'testAdmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Test Kitchen',
            'email' => 'testKitchen@example.com',
            'password' => bcrypt('password'),
            'role' => 'kitchen',
        ]);
    }
}
