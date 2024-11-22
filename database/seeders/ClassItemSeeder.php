<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassItem;
use Illuminate\Support\Facades\DB;

class ClassItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('class_items')->insert([
            [
                'code' => 'RM',
                'description' => 'Raw Material',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'code' => 'FG',
                'description' => 'Finished Good',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'WST',
                'description' => 'Waste',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
        // $classitem = ClassItem::factory()->create([
        //     [
        //     'class_id' => 'RM',
        //     'class_description' => 'Raw Material'
        //     ],

        //     [
        //         'class_id' => 'FG',
        //         'class_description' => 'Finished Good'
        //     ]
        // ]);
    }
}
