<?php

namespace Database\Seeders;

use App\Models\User;
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
        $cat = \App\Models\Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        // User::factory(10)->create();
        \Illuminate\Support\Facades\Artisan::call('passport:client', [
        '--personal' => true,
        '--name' => 'Laravel Personal Access Client',
        '--no-interaction' => true,
    ]);
        \App\Models\User::create([
        'name' => 'Owner User',
        'email' => 'owner@gmail.com',
        'password' => bcrypt('12345678'),
        'role' => 'owner',
    ]);
        \App\Models\User::create([

        'name' => 'User',
        'email' => 'user1@gmail.com',
        'password' => bcrypt('12345678'),
        'role' => 'user',
    
        ]);
        \App\Models\User::create([

        'name' => 'User',
        'email' => 'user2@gmail.com',
        'password' => bcrypt('12345678'),
        'role' => 'user',
    
        ]);
    }
}
