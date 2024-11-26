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
                'password' => bcrypt('admin123')
            ],
            [
                'name' => 'Luis',
                'email' => 'sin.luis@pbn.co.id',
                'password' => bcrypt('luis123')
            ],
            [
                'name' => 'Rudi',
                'email' => 'rudi.cahyadi@pbn.co.id',
                'password' => bcrypt('rudi123')
            ],
            [
                'name' => 'Andreas',
                'email' => 'andreas.chendra@pbn.co.id',
                'password' => bcrypt('andreas123')
            ]
        ];
        
        // Create role once
        $role = Role::create(['name' => 'Admin']);
        
        // Loop through users and create them with roles
        foreach ($users as $userData) {
            $user = User::factory()->create($userData);
            $user->assignRole($role);
        }
    }
}
