<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'nama' => 'Analytics Team',
                'email' => 'analytics@siperah.com',
                'password' => 'analytics123',
                'role' => 'admin', // Changed from 'tim_analytics' because db ENUM only allows 'peternak','pengelola','admin'
            ],
            [
                'nama' => 'Friska Pengelola',
                'email' => 'friska@siperah.com',
                'password' => 'password123',
                'role' => 'pengelola',
            ],
        ];

        foreach ($accounts as $acc) {
            \App\Models\User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'nama' => $acc['nama'],
                    'password' => \Illuminate\Support\Facades\Hash::make($acc['password']),
                    'role' => $acc['role'],
                    'status' => 'aktif',
                ]
            );
        }
        
        $this->command->info('Management accounts synchronized successfully.');
    }
}
