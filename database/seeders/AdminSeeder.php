<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = User::where('username', 'admin')->exists();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'status' => 'active'
            ]);
            
            $this->command->info('Admin default berhasil dibuat!');
            $this->command->info('Username: admin');
            $this->command->info('Password: admin123');
        } else {
            $this->command->info('Admin sudah ada, melewati pembuatan admin default.');
        }
    }
}
