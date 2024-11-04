<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'uofm_id' => 1,
                'class_id' => 1,
                'code' => 'WR-001',
                'description' => 'Skipjack',
                'long_description' => 'FROZEN SKIPJACK TUNA',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'uofm_id' => 1,
                'class_id' => 1,
                'code' => 'WR-002',
                'description' => 'Yellowfin',
                'long_description' => 'FROZEN YELLOWFIN TUNA',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uofm_id' => 1,
                'class_id' => 2,
                'code' => 'FG-001',
                'description' => 'Skipjack Loin Blue 5',
                'long_description' => 'FROZEN SKIPJACK TUNA',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
