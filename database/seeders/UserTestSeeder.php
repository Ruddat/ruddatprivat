<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTestSeeder extends Seeder
{
    public function run(): void
    {
        // Admin anlegen
        Admin::updateOrCreate(
            ['email' => 'admin@admin.lt'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ],
        );

        // Customer anlegen
        Customer::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Max Mustermann',
                'password' => Hash::make('password123'),
            ],
        );
    }
}
