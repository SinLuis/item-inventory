<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('documents')->insert([
            [
                'code' => 'Adjustment',
                'description' => 'Dokumen Kesesaian BB',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'BC 2.5',
                'description' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'PIB',
                'description' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'PIB Lokal',
                'description' => 'Dokumen non Import',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'PPFTZ',
                'description' => '',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
