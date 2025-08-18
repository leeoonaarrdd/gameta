<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample members
        Member::create([
            'username' => 'member1',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'balance' => 50000,
            'status' => 'active',
            'password' => Hash::make('password'),
        ]);

        Member::create([
            'username' => 'member2',
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '081234567891',
            'balance' => 100000,
            'status' => 'active',
            'password' => Hash::make('password'),
        ]);

        Member::create([
            'username' => 'member3',
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'phone' => '081234567892',
            'balance' => 25000,
            'status' => 'inactive',
            'password' => Hash::make('password'),
        ]);

        // Create additional random members
        Member::factory(10)->create();
        
        $this->command->info('Sample members berhasil dibuat!');
    }
}
