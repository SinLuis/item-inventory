<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('permissions')->insert([
            [
                'name' => 'Create Role',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'View Role',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Edit Role',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Delete Role',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Create Permission',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'View Permission',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Edit Permission',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Delete Permission',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Create User',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'View User',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Edit User',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'name' => 'Delete User',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
