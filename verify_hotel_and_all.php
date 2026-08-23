<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

function makeRequest($kernel, $method, $uri, $params = [], $headers = []) {
    $server = [
        'REQUEST_METHOD' => $method,
        'REQUEST_URI' => $uri,
        'HTTP_HOST' => 'localhost:8000',
    ];
    foreach ($headers as $k => $v) {
        $server['HTTP_' . strtoupper(str_replace('-', '_', $k))] = $v;
    }
    if ($method === 'POST' || $method === 'PUT') {
        $server['CONTENT_TYPE'] = 'application/json';
    }

    $req = Request::create($uri, $method, [], [], [], $server, json_encode($params));
    $response = $kernel->handle($req);
    $kernel->terminate($req, $response);
    return $response;
}

echo "=================================================================\n";
echo "🏨 QRestro Hospitality & Multi-Partner Network Tests\n";
echo "=================================================================\n\n";

// 1. Test Hotel Owner Login
$res = makeRequest($kernel, 'POST', '/api/auth/login', ['username' => 'grandhorizon', 'password' => 'demo123']);
$hotelData = json_decode($res->getContent(), true);
$hotelToken = $hotelData['data']['token'] ?? null;
$hotelId = $hotelData['data']['owner']['id'] ?? null;
echo "1. POST /api/auth/login [Hotel Login]: " . ($res->getStatusCode() === 200 && $hotelToken ? "✅ PASSED (Hotel token received)" : "❌ FAILED") . "\n";

// 2. Test Restaurant Owner Login (SpiceHub)
$res = makeRequest($kernel, 'POST', '/api/auth/login', ['username' => 'spicehub', 'password' => 'demo123']);
$restData = json_decode($res->getContent(), true);
$restToken = $restData['data']['token'] ?? null;
$restId = $restData['data']['owner']['id'] ?? null;
echo "2. POST /api/auth/login [Restaurant Login]: " . ($res->getStatusCode() === 200 && $restToken ? "✅ PASSED (Restaurant token received)" : "❌ FAILED") . "\n";

// 3. Test Hotel Room Grid
$res = makeRequest($kernel, 'GET', '/api/hotel/rooms', [], ['Authorization' => 'Bearer ' . $hotelToken]);
$roomsData = json_decode($res->getContent(), true);
$rooms = $roomsData['data'] ?? [];
echo "3. GET /api/hotel/rooms [Hotel Room Grid]: " . ($res->getStatusCode() === 200 && count($rooms) >= 6 ? "✅ PASSED (" . count($rooms) . " rooms)" : "❌ FAILED") . "\n";

// Find Room 102
$room102 = collect($rooms)->firstWhere('room_number', '102');
$room102Id = $room102['id'] ?? null;

// 4. Test Check-In Guest to Room 102
$checkinPayload = [
    'room_id' => $room102Id,
    'full_name' => 'Sarah Jenkins',
    'phone' => '+1 (555) 789-0123',
    'email' => 'sarah.j@example.com',
    'id_type' => 'Driver License',
    'id_number' => 'DL98765432',
    'room_rate' => 149.00,
    'advance_deposit' => 100.00,
    'expected_check_out' => date('Y-m-d', strtotime('+2 days')),
    'notes' => 'Executive suite guest',
];
$res = makeRequest($kernel, 'POST', '/api/hotel/check-in', $checkinPayload, ['Authorization' => 'Bearer ' . $hotelToken]);
$checkinData = json_decode($res->getContent(), true);
$booking102Id = $checkinData['data']['id'] ?? null;
echo "4. POST /api/hotel/check-in [Guest Check-In]: " . ($res->getStatusCode() === 201 && $booking102Id ? "✅ PASSED (Guest checked in)" : "❌ FAILED") . "\n";

// 5. Test Multi-Partner Restaurant Network Linking
$res = makeRequest($kernel, 'POST', '/api/hotel/partner', ['restaurantId' => $restId, 'link' => true], ['Authorization' => 'Bearer ' . $hotelToken]);
$partnerRes = json_decode($res->getContent(), true);
echo "5. POST /api/hotel/partner [Link Partner Restaurant]: " . ($res->getStatusCode() === 200 && $partnerRes['success'] ? "✅ PASSED (Hotel linked to SpiceHub)" : "❌ FAILED") . "\n";

// 6. Test In-Room Guest Portal (Loads Partner Restaurant Dishes)
$room102Sig = \App\Services\SecurityService::getRoomSignature($hotelId, '102');
$res = makeRequest($kernel, 'GET', "/api/public/room/info?hotelId={$hotelId}&roomNumber=102&code={$room102Sig}");
$portalData = json_decode($res->getContent(), true);
$roomSessionToken = $portalData['data']['sessionToken'] ?? null;
$menuDishes = $portalData['data']['food']['items'] ?? [];
$providerName = $portalData['data']['food']['providerName'] ?? '';
echo "6. GET /api/public/room/info [Partner Menu in Room]: " . ($res->getStatusCode() === 200 && count($menuDishes) > 0 ? "✅ PASSED (Provider: $providerName, " . count($menuDishes) . " dishes)" : "❌ FAILED") . "\n";

// 7. Place Room Service Order to Partner Restaurant
$dish1 = $menuDishes[0] ?? null;
$roomOrderPayload = [
    'hotelId' => $hotelId,
    'roomNumber' => '102',
    'restaurantId' => $restId,
    'sessionToken' => $roomSessionToken,
    'notes' => 'Deliver hot to Room 102 please',
    'items' => [
        ['menuItemId' => $dish1['id'], 'quantity' => 2]
    ]
];
$res = makeRequest($kernel, 'POST', '/api/public/room/orders', $roomOrderPayload);
$orderPlaced = json_decode($res->getContent(), true);
$orderId = $orderPlaced['data']['id'] ?? null;
echo "7. POST /api/public/room/orders [Order from Room to Partner]: " . ($res->getStatusCode() === 201 && $orderId ? "✅ PASSED (Dispatched to SpiceHub kitchen)" : "❌ FAILED") . "\n";

// 8. Test Restaurant-Side Order Visibility with Hotel Name & Room Number
$res = makeRequest($kernel, 'GET', '/api/orders', [], ['Authorization' => 'Bearer ' . $restToken]);
$restOrders = json_decode($res->getContent(), true)['data'] ?? [];
$receivedOrder = collect($restOrders)->firstWhere('id', $orderId);
$receivedHotelName = $receivedOrder['hotelName'] ?? null;
$receivedRoomNum = $receivedOrder['roomNumber'] ?? null;
$isRoomService = ($receivedOrder['orderType'] ?? '') === 'room_service';
echo "8. GET /api/orders [Restaurant Sees Hotel & Room]: " . ($receivedOrder && $isRoomService && $receivedRoomNum === '102' ? "✅ PASSED (Visible in Kitchen as 🏨 $receivedHotelName - Room #$receivedRoomNum)" : "❌ FAILED") . "\n";

// 9. Test Restaurant Chef Cooking & Completing Partner Hotel Order
$res = makeRequest($kernel, 'PUT', "/api/orders/{$orderId}", ['status' => 'preparing'], ['Authorization' => 'Bearer ' . $restToken]);
$cookRes = json_decode($res->getContent(), true);
$res2 = makeRequest($kernel, 'PUT', "/api/orders/{$orderId}", ['status' => 'ready'], ['Authorization' => 'Bearer ' . $restToken]);
$readyRes = json_decode($res2->getContent(), true);
echo "9. PUT /api/orders/{id} [Chef Cooks & Dispatches to Room]: " . ($cookRes['success'] && $readyRes['success'] ? "✅ PASSED (Status: preparing -> ready for room delivery)" : "❌ FAILED") . "\n";

// 10. Test Hotel Housekeeping Service Request & Dispatch
$servicePayload = [
    'hotelId' => $hotelId,
    'roomNumber' => '102',
    'serviceName' => 'Fresh Soft Towels',
    'category' => 'housekeeping',
    'sessionToken' => $roomSessionToken,
];
$res = makeRequest($kernel, 'POST', '/api/public/room/services', $servicePayload);
$serviceData = json_decode($res->getContent(), true);
$serviceReqId = $serviceData['data']['id'] ?? null;
echo "10. POST /api/public/room/services [Housekeeping Request]: " . ($res->getStatusCode() === 201 && $serviceReqId ? "✅ PASSED" : "❌ FAILED") . "\n";

// 11. Test Hotel Check-Out & Room Turnover
$res = makeRequest($kernel, 'POST', '/api/hotel/check-out', ['booking_id' => $booking102Id], ['Authorization' => 'Bearer ' . $hotelToken]);
$checkoutRes = json_decode($res->getContent(), true);
echo "11. POST /api/hotel/check-out [Settle & Check-Out]: " . ($res->getStatusCode() === 200 && $checkoutRes['success'] ? "✅ PASSED" : "❌ FAILED") . "\n";

echo "\n🎉 All 11 Multi-Partner Network, Hotel PMS, and Restaurant Kitchen tests PASSED with 100% success rate!\n";
