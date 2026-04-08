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
        // User::factory(10)->create();
        \Illuminate\Support\Facades\Artisan::call('passport:client', [
        '--personal' => true,
        '--name' => 'Laravel Personal Access Client',
        '--no-interaction' => true,
    ]);
        \App\Models\User::factory()->create([
        'name' => 'Owner User',
        'email' => 'owner@gmail.com',
        'password' => bcrypt('12345678'),
        'role' => 'owner',
    ]);
    }
}
