<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Create one demo user for each role.
     * 
     * All demo users use password: password123
     */
    public function run(): void
    {
        $users = [
            [
                'name'       => 'Admin Utama',
                'first_name' => 'Admin',
                'last_name'  => 'Utama',
                'email'      => 'superadmin@amvopenscience.id',
                'role'       => 'super_admin',
            ],
            [
                'name'       => 'Dr. Budi Santoso',
                'first_name' => 'Budi',
                'last_name'  => 'Santoso',
                'email'      => 'editor@amvopenscience.id',
                'role'       => 'editor',
            ],
            [
                'name'       => 'Prof. Siti Aminah',
                'first_name' => 'Siti',
                'last_name'  => 'Aminah',
                'email'      => 'reviewer@amvopenscience.id',
                'role'       => 'reviewer',
            ],
            [
                'name'       => 'Ahmad Rizaldi',
                'first_name' => 'Ahmad',
                'last_name'  => 'Rizaldi',
                'email'      => 'author@amvopenscience.id',
                'role'       => 'author',
            ],
            [
                'name'       => 'Dewi Lestari',
                'first_name' => 'Dewi',
                'last_name'  => 'Lestari',
                'email'      => 'reader@amvopenscience.id',
                'role'       => 'reader',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password'          => Hash::make('password123'),
                    'email_verified_at' => now(),
                ])
            );

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }

            $this->command->info("✅ {$role}: {$user->email}");
        }
    }
}
