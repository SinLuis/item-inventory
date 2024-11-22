<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Uofm;
use Illuminate\Support\Facades\DB;

class UofmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('uofms')->insert([
            [
                'code' => 'KG',
                'description' => 'Kilogram',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'L',
                'description' => 'Liter',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'M',
                'description' => 'Meter',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'PCS',
                'description' => 'Pieces',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'Yrd',
                'description' => 'Yard',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
