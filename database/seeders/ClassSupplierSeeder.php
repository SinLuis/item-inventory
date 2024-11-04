<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassSupplier;
use Illuminate\Support\Facades\DB;

class ClassSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('class_suppliers')->insert([
            [
                // 'class_supplier_id' => 1,
                'class_supplier_description' => 'Supplier'
            ],

            [
                // 'class_supplier_id' => 2,
                'class_supplier_description' => 'Subkontrak'
            ]
        ]);
    }
}
