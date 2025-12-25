<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Brand;
use App\Models\SportType;
use Carbon\Carbon;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get IDs for relationships
        $basketballType = SportType::where('name', 'Basketball')->first();
        $footballType = SportType::where('name', 'Football')->first();
        $tennisType = SportType::where('name', 'Tennis')->first();
        $badmintonType = SportType::where('name', 'Badminton')->first();
        $volleyballType = SportType::where('name', 'Volleyball')->first();
        $swimmingType = SportType::where('name', 'Swimming')->first();

        $nike = Brand::where('name', 'Nike')->first();
        $adidas = Brand::where('name', 'Adidas')->first();
        $wilson = Brand::where('name', 'Wilson')->first();
        $spalding = Brand::where('name', 'Spalding')->first();
        $mikasa = Brand::where('name', 'Mikasa')->first();
        $yonex = Brand::where('name', 'Yonex')->first();
        $speedo = Brand::where('name', 'Speedo')->first();
        $generic = Brand::where('name', 'Generic')->first();

        $equipment = [
            // Basketball Equipment
            [
                'name' => 'Basketball - Official Size 7',
                'sport_type_id' => $basketballType?->id,
                'type' => 'Sports',
                'brand_id' => $spalding?->id,
                'model' => 'TF-1000',
                'description' => 'Official size 7 basketball for indoor and outdoor use',
                'quantity' => 20,
                'available_quantity' => 18,
                'minimum_stock_amount' => 5,
                'price' => 45.99,
                'status' => 'available',
                'location' => 'Storage Room A',
                'purchase_date' => Carbon::now()->subMonths(6),
            ],
            [
                'name' => 'Basketball Hoop Net',
                'sport_type_id' => $basketballType?->id,
                'type' => 'Accessories',
                'brand_id' => $spalding?->id,
                'model' => 'Standard Net',
                'description' => 'Replacement basketball net',
                'quantity' => 15,
                'available_quantity' => 12,
                'minimum_stock_amount' => 3,
                'price' => 12.50,
                'status' => 'available',
                'location' => 'Storage Room A',
            ],

            // Football Equipment
            [
                'name' => 'Football - Size 5',
                'sport_type_id' => $footballType?->id,
                'type' => 'Sports',
                'brand_id' => $adidas?->id,
                'model' => 'Tiro League',
                'description' => 'Official size 5 football',
                'quantity' => 25,
                'available_quantity' => 22,
                'minimum_stock_amount' => 5,
                'price' => 35.00,
                'status' => 'available',
                'location' => 'Storage Room B',
            ],
            [
                'name' => 'Football Goal Net',
                'sport_type_id' => $footballType?->id,
                'type' => 'Accessories',
                'brand_id' => $generic?->id,
                'model' => 'Standard',
                'description' => 'Replacement goal net',
                'quantity' => 8,
                'available_quantity' => 6,
                'minimum_stock_amount' => 2,
                'price' => 45.00,
                'status' => 'available',
                'location' => 'Storage Room B',
            ],

            // Tennis Equipment
            [
                'name' => 'Tennis Racket - Professional',
                'sport_type_id' => $tennisType?->id,
                'type' => 'Equipment',
                'brand_id' => $wilson?->id,
                'model' => 'Blade 98',
                'description' => 'Professional tennis racket',
                'quantity' => 30,
                'available_quantity' => 25,
                'minimum_stock_amount' => 8,
                'price' => 199.99,
                'status' => 'available',
                'location' => 'Storage Room C',
            ],
            [
                'name' => 'Tennis Balls - Can of 3',
                'sport_type_id' => $tennisType?->id,
                'type' => 'Sports',
                'brand_id' => $wilson?->id,
                'model' => 'US Open',
                'description' => 'Professional tennis balls',
                'quantity' => 50,
                'available_quantity' => 45,
                'minimum_stock_amount' => 10,
                'price' => 8.99,
                'status' => 'available',
                'location' => 'Storage Room C',
            ],

            // Badminton Equipment
            [
                'name' => 'Badminton Racket - Carbon',
                'sport_type_id' => $badmintonType?->id,
                'type' => 'Equipment',
                'brand_id' => $yonex?->id,
                'model' => 'Astrox 88D',
                'description' => 'Professional badminton racket',
                'quantity' => 40,
                'available_quantity' => 35,
                'minimum_stock_amount' => 10,
                'price' => 179.99,
                'status' => 'available',
                'location' => 'Storage Room D',
            ],
            [
                'name' => 'Shuttlecock - Feather',
                'sport_type_id' => $badmintonType?->id,
                'type' => 'Sports',
                'brand_id' => $yonex?->id,
                'model' => 'AS-50',
                'description' => 'Professional feather shuttlecock',
                'quantity' => 100,
                'available_quantity' => 85,
                'minimum_stock_amount' => 20,
                'price' => 25.00,
                'status' => 'available',
                'location' => 'Storage Room D',
            ],

            // Volleyball Equipment
            [
                'name' => 'Volleyball - Official',
                'sport_type_id' => $volleyballType?->id,
                'type' => 'Sports',
                'brand_id' => $mikasa?->id,
                'model' => 'MVA200',
                'description' => 'Official volleyball',
                'quantity' => 20,
                'available_quantity' => 18,
                'minimum_stock_amount' => 5,
                'price' => 55.00,
                'status' => 'available',
                'location' => 'Storage Room E',
            ],
            [
                'name' => 'Volleyball Net',
                'sport_type_id' => $volleyballType?->id,
                'type' => 'Accessories',
                'brand_id' => $generic?->id,
                'model' => 'Standard',
                'description' => 'Official height volleyball net',
                'quantity' => 6,
                'available_quantity' => 5,
                'minimum_stock_amount' => 2,
                'price' => 120.00,
                'status' => 'available',
                'location' => 'Storage Room E',
            ],

            // Swimming Equipment
            [
                'name' => 'Swimming Goggles',
                'sport_type_id' => $swimmingType?->id,
                'type' => 'Accessories',
                'brand_id' => $speedo?->id,
                'model' => 'Futura Biofuse',
                'description' => 'Anti-fog swimming goggles',
                'quantity' => 50,
                'available_quantity' => 45,
                'minimum_stock_amount' => 10,
                'price' => 24.99,
                'status' => 'available',
                'location' => 'Storage Room F',
            ],
            [
                'name' => 'Kickboard',
                'sport_type_id' => $swimmingType?->id,
                'type' => 'Equipment',
                'brand_id' => $speedo?->id,
                'model' => 'Standard',
                'description' => 'Training kickboard',
                'quantity' => 15,
                'available_quantity' => 12,
                'minimum_stock_amount' => 3,
                'price' => 18.99,
                'status' => 'available',
                'location' => 'Storage Room F',
            ],

            // Low Stock Items (for testing)
            [
                'name' => 'Basketball Pump',
                'sport_type_id' => $basketballType?->id,
                'type' => 'Accessories',
                'brand_id' => $generic?->id,
                'model' => 'Standard',
                'description' => 'Basketball air pump',
                'quantity' => 4,
                'available_quantity' => 2,
                'minimum_stock_amount' => 3,
                'price' => 15.00,
                'status' => 'available',
                'location' => 'Storage Room A',
            ],
            [
                'name' => 'Tennis Net',
                'sport_type_id' => $tennisType?->id,
                'type' => 'Accessories',
                'brand_id' => $generic?->id,
                'model' => 'Standard',
                'description' => 'Tennis court net',
                'quantity' => 3,
                'available_quantity' => 1,
                'minimum_stock_amount' => 2,
                'price' => 150.00,
                'status' => 'maintenance',
                'location' => 'Storage Room C',
            ],
        ];

        foreach ($equipment as $item) {
            Equipment::updateOrCreate(
                [
                    'name' => $item['name'],
                    'model' => $item['model'] ?? null,
                ],
                $item
            );
        }
    }
}

