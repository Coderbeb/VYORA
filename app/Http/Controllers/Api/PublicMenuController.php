<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Table;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\SecurityService;

class PublicMenuController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = $request->query('ownerId');
        $tableNumber = (int) $request->query('tableNumber');
        $code = $request->query('code');
        $sessionToken = $request->query('sessionToken');

        if (!$ownerId || !$tableNumber) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'MISSING_FIELDS', 'message' => 'ownerId and tableNumber are required']
            ], 400);
        }

        $owner = Owner::where('id', $ownerId)->first(['id', 'restaurant_name']);
        if (!$owner) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Restaurant not found']], 404);
        }

        $table = Table::where('owner_id', $ownerId)
            ->where('table_number', $tableNumber)
            ->where('is_active', true)
            ->first();

        if (!$table) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Table not found or is currently inactive']], 404);
        }

        $isValid = false;
        $isExpiredToken = false;

        if ($code) {
            $expectedSignature = SecurityService::getTableSignature($ownerId, $tableNumber);
            if ($code === $expectedSignature) {
                $isValid = true;
            }
        } elseif ($sessionToken) {
            if (SecurityService::verifySessionToken($sessionToken, $table->updated_at)) {
                $isValid = true;
            } else {
                $isExpiredToken = true;
            }
        }

        if (!$isValid) {
            if ($isExpiredToken) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'SESSION_EXPIRED', 'message' => 'Dining session has expired. Please scan the table QR code again to start a new session.']
                ], 403);
            }
            return response()->json([
                'success' => false,
                'error' => ['code' => 'INVALID_SIGNATURE', 'message' => 'Access Denied: Please scan the table QR code to view the menu.']
            ], 403);
        }

        $nextSessionToken = SecurityService::generateSessionToken($table->id, $table->updated_at);

        $categories = MenuCategory::where('owner_id', $ownerId)
            ->orderBy('sort_order', 'asc')
            ->get(['id', 'name', 'sort_order']);

        $items = MenuItem::where('owner_id', $ownerId)
            ->where('is_available', true)
            ->with(['category' => function ($q) {
                $q->select('id', 'name', 'sort_order');
            }])
            ->orderBy('category_id', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $formatted = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'imageUrl' => $item->image_url,
                'preparationTime' => $item->preparation_time,
                'isVeg' => (bool) ($item->is_veg ?? true),
                'isAvailable' => (bool) $item->is_available,
                'categoryId' => $item->category_id,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                    'sortOrder' => $item->category->sort_order,
                ] : null,
            ];
        });

        $categorized = $categories->map(function ($cat) use ($formatted) {
            $catItems = $formatted->filter(fn($i) => $i['categoryId'] === $cat->id)->values();
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'sortOrder' => $cat->sort_order,
                'items' => $catItems,
            ];
        })->filter(fn($cat) => count($cat['items']) > 0)->values();

        $uncategorized = $formatted->filter(fn($i) => empty($i['categoryId']))->values();

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => [
                    'id' => $owner->id,
                    'restaurantName' => $owner->restaurant_name,
                ],
                'items' => $formatted,
                'categories' => $categorized,
                'uncategorized' => $uncategorized,
                'sessionToken' => $nextSessionToken,
            ]
        ]);
    }

    public function featured(Request $request)
    {
        $restaurants = Owner::where('role', 'RESTAURANT_OWNER')
            ->where('show_on_landing', true)
            ->get(['id', 'restaurant_name as restaurantName', 'cuisine']);

        return response()->json(['success' => true, 'data' => $restaurants]);
    }
}
