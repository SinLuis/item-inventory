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

            //Supplier
            [ 'supplier_name' => "ACTEMSA, S.A.", 'class_id' => 1, 'address' => "Adva.A Tomada, Poligono Industrial A Tomada 15940", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "ALBA FISHING. LTD.", 'class_id' => 1, 'address' => "", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "ALBACORA, SA", 'class_id' => 1, 'address' => "Poligono Landabaso S/N Edificio Albacora", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "ALMAASY DESIGN TRADING EST", 'class_id' => 1, 'address' => "Post Box 1432 Salalah-211 Sultanate Of Oman", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "CARRIER TRANSICOLD HK LTD", 'class_id' => 1, 'address' => "7/F, CHUNG SHUN KNITTING CENTRE, 1-3 WING YIP STREET", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "CHANGLE MINFA FOOD AQUATIC PRODUCT CO.", 'class_id' => 1, 'address' => "Xiajang, Tantou, Changle, Fujian, China", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "CHARLY FISHERIES", 'class_id' => 1, 'address' => "Mamachanthuruth, Bldg. No. 291, Ward No. NGP5, Kollam", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "COSMIC OCEAN CO.,LTD", 'class_id' => 1, 'address' => "17F.2,No.6, Minquan 2nd Rd.Qianzhen Dist.,", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "DONGSHAN CHANGXIN MARINE PRODUCT", 'class_id' => 1, 'address' => "DAWO FISHPORT TONGLING TOWN DONGSHAN COUNTY FUJIAN", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "FCF CO.,LTD", 'class_id' => 1, 'address' => "28th Fl, No.8 Min Chuan 2D Road Chien Chen District", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "HAINAN QINFU FOODS CO., LTD", 'class_id' => 1, 'address' => "No. 320, WENQING ROAD, NEW URBAN QINGLAN, WENCHANG", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "INTERATUN, L.T.D", 'class_id' => 1, 'address' => "Maison La Rosiere PO Box 117 Victoria", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "ISABELLA FISHING LTD", 'class_id' => 1, 'address' => "Maison La Rosiere-PO Box 117 Victoria-Mahe", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "KASAR FISHING CORPORATION", 'class_id' => 1, 'address' => "PO Box. R Kolonia Pohnpei, FSM #96941", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "KIBU PTE. LTD.", 'class_id' => 1, 'address' => "I Raffles Place, One Raffles Place Tower Two", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "KYOKUYO CO.,LTD", 'class_id' => 1, 'address' => "3-5 Akasaka 3-Chome Minato-ku Tokyo 107-0052", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "NIHON FOOD SUPPLY INC.", 'class_id' => 1, 'address' => "3RD FLR,CYGNET BLDG.,4-CHOME,12-2 TSUKIJI,CHUO-KU", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "NINGBO TODAY FOOD CO., LTD", 'class_id' => 1, 'address' => "No. 38 Zhongxing East Road, Xikou, Ningbo, China", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "OCEAN TREASURE SEAFOOD CO., LTD", 'class_id' => 1, 'address' => "RM 112.IF.No 3. Yu Kang East 2nd Road Kaoshiung Taiwan, ROC.", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "OCEANMORES PTE LTD", 'class_id' => 1, 'address' => "101 Cecil Street #18-13, Singapore 069533", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "P.O.P INTERTRADE COMPANY LIMITED", 'class_id' => 1, 'address' => "832 Ladpraowanghin Road, Lad Prao, Bangkok, 10230", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "PAN PACIFIC FOODS (RMI) INC.", 'class_id' => 1, 'address' => "PO. Box. 1289 Majuro, Marshall Islands MH 96960", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "PEMBA TUNA LTD", 'class_id' => 1, 'address' => "Mpendae Mall 2nd Floor, Room 35 PO Box 4199", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "PRIME SEAFOOD LTDA", 'class_id' => 1, 'address' => "Rua Padre Carapuceiro, 968 Centro Empresarial", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "SHISHI JIN HUANG CHANG FROZEN FOOD CO", 'class_id' => 1, 'address' => "Xiangnong Dock, Xiangzhi Town, Shishi City, Quanzhou", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "SILLA CO.,LTD", 'class_id' => 1, 'address' => "362,Baekjegobun-Ro,Songpa-Gu,Seoul, Korea", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "STAR FISH EXPORTS", 'class_id' => 1, 'address' => "AP 1/21 Alginate Industires Building, Industrial", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "TAIYO A&F CO.,LTD", 'class_id' => 1, 'address' => "4-5 Toyomi-Cho.Chuo-Ku, Tokyo 104-0055, Japan", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "TRI MARINE INTERNATIONAL (PTE) LTD", 'class_id' => 1, 'address' => "15 Fishery Port Road Jurong Industrial Estate", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "UNIVERSAL MARINE FOOD INC.", 'class_id' => 1, 'address' => "8825 53rd Avenue, Elmhurst,NY.11373-4517", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "XIAMEN HAI ZHI YUAN FOOD CO., LTD.", 'class_id' => 1, 'address' => "No. 1-5 Area of Lingshang Shopping Center", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "XIONGXING AQUATIC FOOD CO.,LTD", 'class_id' => 1, 'address' => "SHISHI CI No.6 Nanwei,Xiangzhi Town,Quanzhou", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],

            //Subkontrak
            [ 'supplier_name' => "Cahaya Timur", 'class_id' => 2, 'address' => "Jl. Jakarta Bogor KM...", 'phone' => "021-87902607", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "PT. Ikhlas", 'class_id' => 2, 'address' => "JL. Raya Hajung Udik...", 'phone' => "", 'email' => "021-8677567", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "PT. SKM (sarona kary...", 'class_id' => 2, 'address' => "Jl. Bandengan Utara ...", 'phone' => "021-6612971-2", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'supplier_name' => "PT. Sumber Jaya", 'class_id' => 2, 'address' => "JL. Lanbaw No.83 Bog...", 'phone' => "021-29455017", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],

        ]);
    }
}
