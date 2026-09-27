<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         $this->call([
            UserSeeder::class,
        ]);

    //     User::create([
    //     'name' => 'System Administrator',
    //     'email' => 'admin@example.com',
    //     'password' => Hash::make('password'),
    //     'role' => 'admin',
    // ]);

    //    User::create([
    //     'name' => 'Head of department',
    //     'email' => 'hod@example.com',
    //     'password' => Hash::make('password'),
    //     'role' => 'hod',
    // ]);

    // User::create([
    //     'name' => 'Employee',
    //     'email' => 'employee@example.com',
    //     'password' => Hash::make('password'),
    //     'role' => 'employee',
    // ]);
   

    }
}
