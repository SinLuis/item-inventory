<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@pbn.co.id',
                'password' => bcrypt('admin123'),
                'role' => 'Admin'
            ],
            [
                'name' => 'Luis',
                'email' => 'sin.luis@pbn.co.id',
                'password' => bcrypt('luis123'),
                'role' => 'Creator'
            ],
            [
                'name' => 'Rudi',
                'email' => 'rudi.cahyadi@pbn.co.id',
                'password' => bcrypt('rudi123'),
                'role' => 'Viewer'
            ],
            [
                'name' => 'Andreas',
                'email' => 'andreas.chendra@pbn.co.id',
                'password' => bcrypt('andreas123'),
                'role' => 'Creator'
            ]
        ];

        // Create roles
        $roles = [
            'Admin' => Role::firstOrCreate(['name' => 'Admin']),
            'Creator' => Role::firstOrCreate(['name' => 'Creator']),
            'Viewer' => Role::firstOrCreate(['name' => 'Viewer']),
        ];

        // Loop through users and create them with roles
        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']); // Remove role from user data before creating the user

            $user = User::factory()->create($userData);
            $user->assignRole($roles[$roleName]);
        }
    }
}
