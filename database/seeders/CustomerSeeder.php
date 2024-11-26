<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->insert([
            [ 'customer_name' => "ASIAN ALLIANCE INTERNATIONAL COMPANY", 'address' => "8/8 Moo 3,Rama 2 Road,Banbor,Muang,Samutsakorn", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "B.P.S INTER-TRADE (1995) CO.,LTD", 'address' => "99 MMO 3.T.Samnakthong,A.Muang,Rayong 21100, Th", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "BUMBLE BEE FOODS, LLC", 'address' => "13100 Artic Circle Santa Fe Springs, CA 90670", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "CAPITAL FOOD INTERNATIONAL CO.,LTD", 'address' => "888 Mec Tower Building 14th Floor, Thailand", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "CARGILL JAPAN LLC", 'address' => "Toshoku Bussiness Unit, Processed Food Dept. Koku", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "CARGILL SIAM LIMITED", 'address' => "", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "CENTURY PACIFIC FOOD INC.", 'address' => "7Th Flr Centerpoint Building Julia Vargas Avenue", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "CHICKEN OF THE SEA INT'L", 'address' => "2150 East Grand Ave El Segundo, CA 90245", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "COSMIC OCEAN CO., LTD.", 'address' => "17F.-2, No. 6, Minquan 2nd RD., Qianzen Dist., Ka", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "DSM MARINE LIPIDS PERU S.A.C", 'address' => "Peru", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "EUROFISH. SA", 'address' => "Ciudadela Arroyo Azul,Calle Transmarina SN Y AV", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "FISHMEAL MARKETING DEVELOPMENT CO., LTD", 'address' => "52 Mood 6 Praram 2 Road, KM 72 Tambol Klorngkhon", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "GENERAL TUNA CORPORATION", 'address' => "Purok Lansong, Barangay Tambler, Philippines", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "HANWA CO., LTD.", 'address' => "1-13-1 TSUKIJI, CHUO-KU TOKYO 104-8429", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "I-TAIL CORPORATION PUBLIC COMPANY LIMITED", 'address' => "979/92-94 29TH Floor, S.M. Tower, Bangkok", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "ITOCHU CORPORATION TOKEA SECTION", 'address' => "5-1, Kita-Aoyama 2-Chome, Minato-Ku, Tokyo 107-807", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "KIBU PTE.LTD.", 'address' => "I Raffles Place, One Raffles Place Tower Two, #27", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "MITSUBISHI CORPORATION", 'address' => "Manurouchi Park Bldg,6-1,Marunouchi 2-Chome", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "MRC CO., LTD", 'address' => "12 Iwado-Cho Makurazaki-City Kagoshima, Japan", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "NAHAR POULTRY LIMITED", 'address' => "Bangladesh", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "NICHIMO CO., LTD", 'address' => "2-20, HIGASHI-SHINAGAWA 2-CHOME", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "NIHON FOOD SUPPLY INC.", 'address' => "3rd FLR, Cygnet Bldg,4-Chome,12-2, Tokyo", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "NUTRIX PUBLIC COMPANY LIMITED", 'address' => "55 Soi Serithai 87, Bangchan Industrial Estate,", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "P.O.P INTERTRADE CO.,LTD.", 'address' => "832 LADPRAOWANGHIN ROAD LADPRAO, BANGKOK, THAILAN", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "P.P.S. TUNA CO.,LTD", 'address' => "31/98, Moo 3, SOI Pattana SOI 1", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "PESCADOS INDUSTRILIZADOS SA DE CV", 'address' => "AV Puerto De Mazatlan 406 A C P 82050", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "PHIL BEST CANNING CORPORATION", 'address' => "Tambler, General Santos City, Philippines", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "RD TUNA CANNERS LTD.", 'address' => "Portion 1004 North Coast Road Siar PO. BOX 2113,", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "RS CANNERY COMPANY LIMITED", 'address' => "255/1 INDUSTRIAL SOI 3, BANGPOO INDUSTRIAL ESTATE", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "SARL HAAL", 'address' => "Hispano Algerienne De L'Alimentation Zone Industr", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "SARL RAJA FOOD INDUSTRIE", 'address' => "Zone Industrielle Hassi Ameur Oran", 'phone' => "40211077", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "SIAM INTERNATIONAL FOOD CO., LTD", 'address' => "88, Moo 10, T.Natub, A.Jana, Songkhla 90130", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "SOUTHEAST ASIAN PACKAGING AND CANNING", 'address' => "233 MOO 4 BANGPOO INDUSTRIAL ESTATE SUKHUMVIT ROAD", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "SPA CONSERVERIE SOLEILD'ALGERIE", 'address' => "RN No. 11 Bassin Daoud Habib Hassi Ben", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "SUARCEM HOLDING S.A.DE C.V.", 'address' => "Calle Central Oriente NUM 5 Parque Industrial", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "TAIYO A&F CO.LTD", 'address' => "2-5-20 Nakaminato,Yaizu City,Shizuoka", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "THAI UNION MANUFACTURING COMPANY LIMITED", 'address' => "979/13-16,M Floor,SM Tower,Phahol Yothin Road", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "TRI MARINE INTERNATIONAL (PTE) LTD", 'address' => "15 FISHERY PORT ROAD JURONG INDUSTRIAL ESTATE", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],
            [ 'customer_name' => "TROPICAL CANNING (THAILAND) PUBLIC CO.,LTD", 'address' => "1/1 M.2,T.THUNGYAI,HATYAI, SONGKHLA 90110, THAILAN", 'phone' => "", 'email' => "", 'pic' => "", 'created_at' => now(), 'updated_at' => now() ],

        ]);
    }
}
