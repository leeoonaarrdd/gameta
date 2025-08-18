<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class TestMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Member::create([
            'username' => 'member',
            'name' => 'Member Test',
            'email' => 'member@test.com',
            'phone' => '081234567890',
            'balance' => 100000,
            'status' => 'active',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
