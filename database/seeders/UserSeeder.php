<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('12345678'), 
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'CS1',
            'username' => 'penggunacs1',
            'password' => Hash::make('12345678'),
            'role' => 'customerservice',
        ]);
    }
}
