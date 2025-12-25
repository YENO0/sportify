<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Nike',
                'description' => 'Just Do It - Premium sports equipment and apparel',
                'website' => 'https://www.nike.com',
                'contact_email' => 'contact@nike.com',
            ],
            [
                'name' => 'Adidas',
                'description' => 'Impossible is Nothing - Quality sports gear',
                'website' => 'https://www.adidas.com',
                'contact_email' => 'contact@adidas.com',
            ],
            [
                'name' => 'Wilson',
                'description' => 'Professional tennis and sports equipment',
                'website' => 'https://www.wilson.com',
                'contact_email' => 'contact@wilson.com',
            ],
            [
                'name' => 'Spalding',
                'description' => 'Basketball and sports equipment manufacturer',
                'website' => 'https://www.spalding.com',
                'contact_email' => 'contact@spalding.com',
            ],
            [
                'name' => 'Mikasa',
                'description' => 'Volleyball and sports ball manufacturer',
                'website' => 'https://www.mikasa-sports.com',
                'contact_email' => 'contact@mikasa.com',
            ],
            [
                'name' => 'Yonex',
                'description' => 'Badminton and tennis equipment specialist',
                'website' => 'https://www.yonex.com',
                'contact_email' => 'contact@yonex.com',
            ],
            [
                'name' => 'Speedo',
                'description' => 'Swimming gear and accessories',
                'website' => 'https://www.speedo.com',
                'contact_email' => 'contact@speedo.com',
            ],
            [
                'name' => 'Generic',
                'description' => 'Generic sports equipment',
                'website' => null,
                'contact_email' => null,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['name' => $brand['name']],
                $brand
            );
        }
    }
}

