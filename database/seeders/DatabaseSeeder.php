<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(ClassItemSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(ClassSupplierSeeder::class);
        $this->call(UofmSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(StorageSeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(DocumentSeeder::class);
    }
}
