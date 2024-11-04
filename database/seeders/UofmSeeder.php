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
            ]
        ]);
    }
}
