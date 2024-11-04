<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'supplier_name' => 'TRI MARINE INTERNASIONAL LTD',
                'class_id' => 1,
                'address' => '15 Fishery Port Road Jurong Industrial Estate',
                'phone' => '',
                'email' => '',
                'pic' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'supplier_name' => 'ALBACORA, SA',
                'class_id' => 1,
                'address' => 'Poligono Landabaso S/N Edificio Albacora',
                'phone' => '',
                'email' => '',
                'pic' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'supplier_name' => 'Kasar Fishing Corporation',
                'class_id' => 1,
                'address' => 'PO Box. R Kolonia Pohnpei, FSM #96941',
                'phone' => '',
                'email' => '',
                'pic' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'supplier_name' => 'Cahaya Timur',
                'class_id' => 2,
                'address' => 'Jl. Jakarta Bogor KM.41.2 Cibinong Bogor',
                'phone' => '021 - 87902607',
                'email' => '',
                'pic' => '',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
