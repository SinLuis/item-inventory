<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Storage;
use Illuminate\Support\Facades\DB;

class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('storages')->insert([
            [
                'storage' => 'MB01',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'MB02',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'PBN 1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'PBN 2',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'FG01',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'FG02',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'IDM01',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'IDM02',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'KACS',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'storage' => 'SAMICO',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
