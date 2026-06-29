<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Outlet;
use App\Models\DiningTable;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Dummy Company (Tenant)
        $company = Company::create([
            'name' => 'Sinar Rasa Group',
            'domain' => 'sinarrasa',
            'email' => 'info@sinarrasa.com',
            'is_active' => true
        ]);

        // 2. Buat Karyawan & Owner untuk Company tersebut
        User::create([
            'company_id' => $company->id,
            'username' => 'owner',
            'fullname' => 'Budi Owner',
            'email' => 'owner@smartdine.com',
            'password' => Hash::make('password123'),
            'role' => 'OWNER',
        ]);

        User::create([
            'company_id' => $company->id,
            'username' => 'cashier',
            'fullname' => 'Siti Kasir',
            'email' => 'cashier@smartdine.com',
            'password' => Hash::make('password123'),
            'role' => 'CASHIER',
        ]);

        User::create([
            'company_id' => $company->id,
            'username' => 'kitchen',
            'fullname' => 'Chef Juna Kitchen',
            'email' => 'kitchen@smartdine.com',
            'password' => Hash::make('password123'),
            'role' => 'KITCHEN',
        ]);

        // 3. Buat Outlet Cabang
        $outlet = Outlet::create([
            'company_id' => $company->id,
            'name' => 'Sinar Rasa - Cabang Sudirman',
            'address' => 'Jl. Jend Sudirman No. 12, Jakarta',
            'phone' => '021555123',
            'is_active' => true
        ]);

        // 4. Buat Meja Restoran di Outlet tersebut
        foreach (['M01', 'M02', 'M03', 'M04', 'M05'] as $tableNo) {
            DiningTable::create([
                'outlet_id' => $outlet->id,
                'table_no' => $tableNo,
                'qr_code' => 'https://smartdine.com/scan/' . $outlet->id . '/' . $tableNo,
                'is_active' => true
            ]);
        }

        // 5. Buat Kategori Menu
        $makanan = Category::create([
            'company_id' => $company->id,
            'name' => 'Makanan Utama',
            'icon' => 'utensils',
            'sort_order' => 1
        ]);

        $minuman = Category::create([
            'company_id' => $company->id,
            'name' => 'Minuman',
            'icon' => 'coffee',
            'sort_order' => 2
        ]);

        // 6. Buat Menu Makanan & Minuman
        Menu::create([
            'company_id' => $company->id,
            'category_id' => $makanan->id,
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan telur, ayam suwir, dan kerupuk',
            'price' => 25000,
            'cooking_time' => 10,
            'stock' => 50,
            'is_available' => true
        ]);

        Menu::create([
            'company_id' => $company->id,
            'category_id' => $makanan->id,
            'name' => 'Mie Goreng Jawa',
            'description' => 'Mie goreng bumbu jawa otentik pedas manis',
            'price' => 23000,
            'cooking_time' => 8,
            'stock' => 40,
            'is_available' => true
        ]);

        Menu::create([
            'company_id' => $company->id,
            'category_id' => $minuman->id,
            'name' => 'Es Teh Manis',
            'description' => 'Teh seduh segar dengan es batu kristal',
            'price' => 5000,
            'cooking_time' => 2,
            'stock' => 100,
            'is_available' => true
        ]);
    }
}