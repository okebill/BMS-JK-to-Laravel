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
        // Create or update default admin user
        $user = User::updateOrCreate(
            ['email' => 'admin@bms.okebil.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        // Output untuk konfirmasi
        $this->command->info('User created/updated: ' . $user->email);
        $this->command->info('Password: admin123');
    }
}
