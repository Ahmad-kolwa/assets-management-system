<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'department_id' => null,
            ]
        );

        // Head of Department
        User::updateOrCreate(
            ['email' => 'hod@example.com'],
            [
                'name' => 'Head of Department',
                'password' => Hash::make('password'),
                'role' => 'hod',
                'is_active' => true,
                'department_id' => 1, // change if your department ID is different
            ]
        );

        // Employee
        User::updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'John Employee',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'department_id' => 1, // change if needed
            ]
        );
    }
}