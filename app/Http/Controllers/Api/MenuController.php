<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    private function resolveOwnerId($user): ?string
    {
        if (!$user) return null;
        return !empty($user->owner_id) ? (string) $user->owner_id : (string) ($user->id ?? '');
    }

    private function formatDate($date): string
    {
        if (!$date) return now()->toISOString();
        if (is_string($date)) return $date;
        if (is_object($date) && method_exists($date, 'toISOString')) {
            return $date->toISOString();
        }
        return date('c', strtotime((string) $date));
    }

    public function index(Request $request)
    {
        $ownerId = $request->query('ownerId');
        $available = $request->query('available');

        if (!$ownerId) {
            $user = $request->attributes->get('auth_user');
            $ownerId = $this->resolveOwnerId($user);
            if (!$ownerId) {
                return response()->json(['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Authentication required']], 401);
            }
        }

        $query = MenuItem::where('owner_id', $ownerId)
            ->with(['category' => function ($q) {
                $q->select('id', 'name', 'sort_order');
            }])
            ->orderBy('category_id', 'asc')
            ->orderBy('created_at', 'desc');

        if ($available === 'true') {
            $query->where('is_available', true);
        }

        $items = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'ownerId' => $item->owner_id,
                'categoryId' => $item->category_id,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                    'sortOrder' => (int) $item->category->sort_order,
                ] : null,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'imageUrl' => $item->image_url,
                'preparationTime' => (int) ($item->preparation_time ?? 15),
                'isVeg' => (bool) ($item->is_veg ?? true),
                'isAvailable' => (bool) $item->is_available,
                'createdAt' => $this->formatDate($item->created_at),
                'updatedAt' => $this->formatDate($item->updated_at),
            ];
        });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $this->resolveOwnerId($user);

        if (!$ownerId) {
            return response()->json(['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Authentication required']], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            // Dietary flag
            $isVeg = true;
            if ($request->has('isVeg')) {
                $isVeg = filter_var($request->input('isVeg'), FILTER_VALIDATE_BOOLEAN);
            } elseif ($request->has('is_veg')) {
                $isVeg = filter_var($request->input('is_veg'), FILTER_VALIDATE_BOOLEAN);
            }

            // Availability flag
            $isAvailable = true;
            if ($request->has('isAvailable')) {
                $isAvailable = filter_var($request->input('isAvailable'), FILTER_VALIDATE_BOOLEAN);
            } elseif ($request->has('is_available')) {
                $isAvailable = filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN);
            }

            // Prep time
            $prepTime = max(1, (int) ($request->input('preparationTime') ?: 15));

            // Clean Category ID to prevent Foreign Key constraints
            $catId = $request->input('categoryId');
            if (empty($catId) || $catId === 'null' || $catId === 'undefined' || trim((string)$catId) === '') {
                $catId = null;
            } else {
                $catExists = MenuCategory::where('id', $catId)->where('owner_id', $ownerId)->exists();
                if (!$catExists) {
                    $catId = null;
                }
            }

            $item = MenuItem::create([
                'owner_id' => $ownerId,
                'name' => trim((string) $request->input('name')),
                'description' => trim((string) $request->input('description', '')) ?: null,
                'price' => (float) $request->input('price'),
                'preparation_time' => $prepTime,
                'is_veg' => $isVeg,
                'is_available' => $isAvailable,
                'category_id' => $catId,
                'image_url' => trim((string) $request->input('imageUrl', '')) ?: null,
            ]);

            $item->load('category');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'ownerId' => $item->owner_id,
                    'categoryId' => $item->category_id,
                    'category' => $item->category ? [
                        'id' => $item->category->id,
                        'name' => $item->category->name,
                        'sortOrder' => (int) $item->category->sort_order,
                    ] : null,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => (float) $item->price,
                    'imageUrl' => $item->image_url,
                    'preparationTime' => (int) $item->preparation_time,
                    'isVeg' => (bool) ($item->is_veg ?? true),
                    'isAvailable' => (bool) $item->is_available,
                    'createdAt' => $this->formatDate($item->created_at),
                    'updatedAt' => $this->formatDate($item->updated_at),
                ],
                'message' => 'Dish added successfully'
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Menu store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DATABASE_ERROR', 'message' => 'Failed to save dish: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $this->resolveOwnerId($user);

        if (!$ownerId) {
            return response()->json(['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Authentication required']], 401);
        }

        $item = MenuItem::where('id', $id)->where('owner_id', $ownerId)->first();

        if (!$item) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Item not found']], 404);
        }

        try {
            if ($request->has('name')) {
                $name = trim((string) $request->input('name'));
                if ($name !== '') {
                    $item->name = $name;
                }
            }

            if ($request->has('description') || array_key_exists('description', $request->all())) {
                $desc = trim((string) $request->input('description', ''));
                $item->description = $desc !== '' ? $desc : null;
            }

            if ($request->has('price')) {
                $item->price = (float) $request->input('price');
            }

            if ($request->has('preparationTime')) {
                $item->preparation_time = max(1, (int) $request->input('preparationTime'));
            }

            if ($request->has('isVeg')) {
                $item->is_veg = filter_var($request->input('isVeg'), FILTER_VALIDATE_BOOLEAN);
            } elseif ($request->has('is_veg')) {
                $item->is_veg = filter_var($request->input('is_veg'), FILTER_VALIDATE_BOOLEAN);
            }

            if ($request->has('isAvailable')) {
                $item->is_available = filter_var($request->input('isAvailable'), FILTER_VALIDATE_BOOLEAN);
            } elseif ($request->has('is_available')) {
                $item->is_available = filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN);
            }

            // Handle category_id safely
            if ($request->has('categoryId') || array_key_exists('categoryId', $request->all())) {
                $catId = $request->input('categoryId');
                if (empty($catId) || $catId === 'null' || $catId === 'undefined' || trim((string)$catId) === '') {
                    $item->category_id = null;
                } else {
                    $catExists = MenuCategory::where('id', $catId)->where('owner_id', $ownerId)->exists();
                    $item->category_id = $catExists ? $catId : null;
                }
            }

            if ($request->has('imageUrl') || array_key_exists('imageUrl', $request->all())) {
                $img = trim((string) $request->input('imageUrl', ''));
                $item->image_url = $img !== '' ? $img : null;
            }

            $item->save();
            $item->load('category');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'ownerId' => $item->owner_id,
                    'categoryId' => $item->category_id,
                    'category' => $item->category ? [
                        'id' => $item->category->id,
                        'name' => $item->category->name,
                        'sortOrder' => (int) $item->category->sort_order,
                    ] : null,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => (float) $item->price,
                    'imageUrl' => $item->image_url,
                    'preparationTime' => (int) $item->preparation_time,
                    'isVeg' => (bool) ($item->is_veg ?? true),
                    'isAvailable' => (bool) $item->is_available,
                    'createdAt' => $this->formatDate($item->created_at),
                    'updatedAt' => $this->formatDate($item->updated_at),
                ],
                'message' => 'Dish updated successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('Menu update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DATABASE_ERROR', 'message' => 'Failed to update dish: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $this->resolveOwnerId($user);

        if (!$ownerId) {
            return response()->json(['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Authentication required']], 401);
        }

        $item = MenuItem::where('id', $id)->where('owner_id', $ownerId)->first();

        if (!$item) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Item not found']], 404);
        }

        try {
            $item->delete();
            return response()->json(['success' => true, 'message' => 'Item deleted successfully']);
        } catch (\Throwable $e) {
            Log::error('Menu delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DATABASE_ERROR', 'message' => 'Failed to delete dish: ' . $e->getMessage()]
            ], 500);
        }
    }
}
