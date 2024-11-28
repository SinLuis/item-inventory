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
            //Class ID 1 = RM
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-001", 'description' => "SKIPJACK", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-001.A", 'description' => "SKIPJACK A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-002", 'description' => "YELLOWFIN", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-002.A", 'description' => "YELLOWFIN A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-003", 'description' => "ALBACORE", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-003.A", 'description' => "ALBACORE A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-004", 'description' => "FRIGATE TUNA", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-004.A", 'description' => "FRIGATE TUNA A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-005", 'description' => "BIG EYE", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 1, 'code' => "WR-005.A", 'description' => "BIG EYE A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            //Class ID 2 = FG
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-001.A", 'description' => "LOIN SKIPJACK A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-002", 'description' => "LOIN YELLOWFIN", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-002.A", 'description' => "LOIN YELLOWFIN A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-003", 'description' => "LOIN ALBACORE", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-003.A", 'description' => "LOIN ALBACORE A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-004", 'description' => "FRIGATE TUNA LOIN", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-004.A", 'description' => "FRIGATE TUNA LOIN A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-005", 'description' => "BIG EYE LOIN", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-005.A", 'description' => "BIG EYE LOIN A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-006", 'description' => "FISH MEAL", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-006.A", 'description' => "FISH MEAL A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-007", 'description' => "FISH OIL", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-007.A", 'description' => "FISH OIL A", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'uofm_id' => "1", 'class_id' => 2, 'code' => "FG-008", 'description' => "BIG EYE POUCH", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            //Class ID 3 = WST
            [ 'uofm_id' => "1", 'class_id' => 3, 'code' => "WST-001", 'description' => "WASTE", 'long_description' => "", 'created_at' => now(), 'updated_at' => now() ],
            

        ]);
    }
}
