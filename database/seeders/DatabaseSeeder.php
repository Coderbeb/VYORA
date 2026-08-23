<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\Owner;
use App\Models\Table;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\HotelServiceRequest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\SecurityService;
use App\Services\QRService;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Subscription plans for Restaurant, Hotel, and Combo
        $restaurantFreePlan = SubscriptionPlan::firstOrCreate(
            ['tier' => 'FREE', 'business_type' => 'RESTAURANT'],
            ['price' => 0.00, 'max_tables' => 5, 'max_rooms' => 0, 'features' => ['Basic QR ordering', 'Up to 5 tables']]
        );

        SubscriptionPlan::firstOrCreate(
            ['tier' => 'PRO', 'business_type' => 'RESTAURANT'],
            ['price' => 49.99, 'max_tables' => 25, 'max_rooms' => 0, 'features' => ['Up to 25 tables', 'Priority support', 'Analytics']]
        );

        SubscriptionPlan::firstOrCreate(
            ['tier' => 'PREMIUM', 'business_type' => 'RESTAURANT'],
            ['price' => 99.99, 'max_tables' => 100, 'max_rooms' => 0, 'features' => ['Unlimited tables', 'White-label branding', 'API access']]
        );

        $hotelFreePlan = SubscriptionPlan::firstOrCreate(
            ['tier' => 'FREE', 'business_type' => 'HOTEL'],
            ['price' => 0.00, 'max_tables' => 0, 'max_rooms' => 10, 'features' => ['Complete Hotel PMS', 'Up to 10 rooms', 'In-room QR services']]
        );

        $comboProPlan = SubscriptionPlan::firstOrCreate(
            ['tier' => 'PRO', 'business_type' => 'COMBO'],
            ['price' => 79.99, 'max_tables' => 20, 'max_rooms' => 30, 'features' => ['Both Hotel PMS & Restaurant POS', 'Up to 20 tables & 30 rooms', 'Unified Folio & KDS']]
        );

        // 2. Super Admin
        $admin = Owner::where('role', 'SUPER_ADMIN')->first();
        if (!$admin) {
            Owner::create([
                'username' => 'superadmin',
                'email' => 'admin@vyora.com',
                'password_hash' => SecurityService::hashPassword('admin123'),
                'restaurant_name' => 'Vyora Platform',
                'owner_name' => 'Platform Admin',
                'role' => 'SUPER_ADMIN',
                'business_type' => 'BOTH',
            ]);
        }

        // 3. Demo Restaurant (SpiceHub)
        $spiceHub = Owner::where('username', 'spicehub')->first();
        if (!$spiceHub) {
            $spiceHub = Owner::create([
                'username' => 'spicehub',
                'email' => 'spicehub@demo.com',
                'password_hash' => SecurityService::hashPassword('demo123'),
                'restaurant_name' => 'SpiceHub Dining',
                'owner_name' => 'Sanjay Kapoor',
                'role' => 'RESTAURANT_OWNER',
                'business_type' => 'RESTAURANT',
                'cuisine' => 'Spice & Tandoor',
                'show_on_landing' => true,
                'plan_id' => $restaurantFreePlan->id,
            ]);

            // Create tables with QR codes
            for ($i = 1; $i <= 5; $i++) {
                $orderUrl = QRService::buildTableUrl($spiceHub->id, $i);
                $qrImg = QRService::generateSvgDataUri($orderUrl);
                Table::create([
                    'owner_id' => $spiceHub->id,
                    'table_number' => $i,
                    'qr_code_data' => $orderUrl,
                    'qr_code_image_url' => $qrImg,
                    'is_active' => true,
                ]);
            }

            // Menu Items for SpiceHub
            $catStarters = MenuCategory::create(['owner_id' => $spiceHub->id, 'name' => 'Starters', 'sort_order' => 0]);
            $catMains = MenuCategory::create(['owner_id' => $spiceHub->id, 'name' => 'Main Course', 'sort_order' => 1]);
            $catDrinks = MenuCategory::create(['owner_id' => $spiceHub->id, 'name' => 'Beverages', 'sort_order' => 2]);

            MenuItem::create([
                'owner_id' => $spiceHub->id,
                'category_id' => $catStarters->id,
                'name' => 'Paneer Tikka',
                'description' => 'Char-grilled cottage cheese cubes marinated in aromatic spices and yogurt.',
                'price' => 280.00,
                'preparation_time' => 15,
                'is_available' => true,
            ]);

            MenuItem::create([
                'owner_id' => $spiceHub->id,
                'category_id' => $catMains->id,
                'name' => 'Butter Chicken',
                'description' => 'Tender chicken simmered in rich creamy tomato and cashew butter gravy.',
                'price' => 380.00,
                'preparation_time' => 20,
                'is_available' => true,
            ]);

            MenuItem::create([
                'owner_id' => $spiceHub->id,
                'category_id' => $catMains->id,
                'name' => 'Dal Makhani',
                'description' => 'Slow-cooked black lentils simmered overnight with fresh cream and butter.',
                'price' => 260.00,
                'preparation_time' => 15,
                'is_available' => true,
            ]);

            MenuItem::create([
                'owner_id' => $spiceHub->id,
                'category_id' => $catDrinks->id,
                'name' => 'Mineral Water Bottle (1L)',
                'description' => 'Chilled packaged drinking water bottle.',
                'price' => 30.00,
                'preparation_time' => 2,
                'is_available' => true,
            ]);
        }

        // 4. Demo Hotel (Grand Horizon Hotel & Suites)
        $hotel = Owner::where('username', 'grandhorizon')->first();
        if (!$hotel) {
            $hotel = Owner::create([
                'username' => 'grandhorizon',
                'email' => 'hotel@demo.com',
                'password_hash' => SecurityService::hashPassword('demo123'),
                'restaurant_name' => 'Grand Horizon Hotel & Suites',
                'owner_name' => 'Alexander Vance',
                'role' => 'RESTAURANT_OWNER',
                'business_type' => 'HOTEL',
                'cuisine' => 'Luxury Hospitality & In-Room Dining',
                'show_on_landing' => true,
                'plan_id' => $comboProPlan->id,
                'partner_restaurant_id' => $spiceHub ? $spiceHub->id : null, // Linked partner restaurant
            ]);

            // Room Categories
            $catDeluxe = RoomCategory::create([
                'owner_id' => $hotel->id,
                'name' => 'Deluxe King Suite',
                'base_price' => 149.00,
                'max_occupancy' => 2,
                'amenities' => ['King Bed', 'City View', 'High-Speed Wi-Fi', 'Smart TV', 'Mini Bar', 'Air Conditioning'],
            ]);

            $catExecutive = RoomCategory::create([
                'owner_id' => $hotel->id,
                'name' => 'Executive Twin Room',
                'base_price' => 119.00,
                'max_occupancy' => 2,
                'amenities' => ['Twin Beds', 'Work Desk', 'Wi-Fi', 'Coffee Maker', 'Shower'],
            ]);

            // Rooms 101-106
            $rooms = [
                ['num' => '101', 'cat' => $catDeluxe->id, 'floor' => 1, 'status' => 'occupied'],
                ['num' => '102', 'cat' => $catDeluxe->id, 'floor' => 1, 'status' => 'vacant_clean'],
                ['num' => '103', 'cat' => $catExecutive->id, 'floor' => 1, 'status' => 'vacant_clean'],
                ['num' => '104', 'cat' => $catExecutive->id, 'floor' => 1, 'status' => 'vacant_dirty'],
                ['num' => '201', 'cat' => $catDeluxe->id, 'floor' => 2, 'status' => 'vacant_clean'],
                ['num' => '202', 'cat' => $catExecutive->id, 'floor' => 2, 'status' => 'maintenance'],
            ];

            $createdRooms = [];
            foreach ($rooms as $r) {
                $roomUrl = QRService::buildRoomUrl($hotel->id, $r['num']);
                $qrImage = QRService::generateSvgDataUri($roomUrl);

                $createdRooms[$r['num']] = Room::create([
                    'owner_id' => $hotel->id,
                    'category_id' => $r['cat'],
                    'room_number' => $r['num'],
                    'floor' => $r['floor'],
                    'status' => $r['status'],
                    'qr_code_data' => $roomUrl,
                    'qr_code_image_url' => $qrImage,
                ]);
            }

            // Demo Guest & Active Booking for Room 101
            $demoGuest = Guest::create([
                'owner_id' => $hotel->id,
                'full_name' => 'David Miller',
                'phone' => '+1 (555) 234-5678',
                'email' => 'david.miller@example.com',
                'id_type' => 'Passport',
                'id_number' => 'P12938475',
            ]);

            $booking101 = Booking::create([
                'owner_id' => $hotel->id,
                'room_id' => $createdRooms['101']->id,
                'guest_id' => $demoGuest->id,
                'check_in' => Carbon::now()->subHours(18),
                'expected_check_out' => Carbon::now()->addHours(30),
                'room_rate' => 149.00,
                'advance_deposit' => 50.00,
                'status' => 'active',
                'notes' => 'Early check-in requested, high floor preference.',
            ]);

            // Sample Housekeeping Request
            HotelServiceRequest::create([
                'owner_id' => $hotel->id,
                'room_id' => $createdRooms['101']->id,
                'booking_id' => $booking101->id,
                'service_name' => 'Extra Soft Towels & Bathrobe',
                'category' => 'housekeeping',
                'status' => 'pending',
                'notes' => 'Guest requested 2 extra bath towels.',
            ]);
        }
    }
}
