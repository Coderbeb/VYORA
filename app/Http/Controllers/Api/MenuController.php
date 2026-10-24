<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = $request->query('ownerId');
        $available = $request->query('available');

        if (!$ownerId) {
            $user = $request->attributes->get('auth_user');
            if (!$user) {
                return response()->json(['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Authentication required']], 401);
            }
            $ownerId = $user->id;
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
                    'sortOrder' => $item->category->sort_order,
                ] : null,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'imageUrl' => $item->image_url,
                'preparationTime' => $item->preparation_time,
                'isVeg' => (bool) ($item->is_veg ?? true),
                'isAvailable' => (bool) $item->is_available,
                'createdAt' => $item->created_at->toISOString(),
                'updatedAt' => $item->updated_at->toISOString(),
            ];
        });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $isVeg = true;
        if ($request->has('isVeg')) {
            $isVeg = filter_var($request->input('isVeg'), FILTER_VALIDATE_BOOLEAN);
        } elseif ($request->has('is_veg')) {
            $isVeg = filter_var($request->input('is_veg'), FILTER_VALIDATE_BOOLEAN);
        }

        $item = MenuItem::create([
            'owner_id' => $user->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => (float) $request->input('price'),
            'preparation_time' => (int) ($request->input('preparationTime') ?: 15),
            'is_veg' => $isVeg,
            'is_available' => $request->input('isAvailable', true) !== false,
            'category_id' => $request->input('categoryId'),
            'image_url' => $request->input('imageUrl'),
        ]);

        $item->load('category');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $item->id,
                'ownerId' => $item->owner_id,
                'categoryId' => $item->category_id,
                'category' => $item->category,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'imageUrl' => $item->image_url,
                'preparationTime' => $item->preparation_time,
                'isVeg' => (bool) ($item->is_veg ?? true),
                'isAvailable' => (bool) $item->is_available,
                'createdAt' => $item->created_at->toISOString(),
                'updatedAt' => $item->updated_at->toISOString(),
            ]
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $item = MenuItem::where('id', $id)->where('owner_id', $user->id)->first();

        if (!$item) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Item not found']], 404);
        }

        if ($request->has('name')) $item->name = $request->input('name');
        if ($request->has('description')) $item->description = $request->input('description');
        if ($request->has('price')) $item->price = (float) $request->input('price');
        if ($request->has('preparationTime')) $item->preparation_time = (int) $request->input('preparationTime');
        if ($request->has('isVeg')) $item->is_veg = filter_var($request->input('isVeg'), FILTER_VALIDATE_BOOLEAN);
        elseif ($request->has('is_veg')) $item->is_veg = filter_var($request->input('is_veg'), FILTER_VALIDATE_BOOLEAN);
        if ($request->has('isAvailable')) $item->is_available = (bool) $request->input('isAvailable');
        if ($request->has('categoryId')) $item->category_id = $request->input('categoryId');
        if ($request->has('imageUrl')) $item->image_url = $request->input('imageUrl');

        $item->save();
        $item->load('category');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $item->id,
                'ownerId' => $item->owner_id,
                'categoryId' => $item->category_id,
                'category' => $item->category,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'imageUrl' => $item->image_url,
                'preparationTime' => $item->preparation_time,
                'isVeg' => (bool) ($item->is_veg ?? true),
                'isAvailable' => (bool) $item->is_available,
                'createdAt' => $item->created_at->toISOString(),
                'updatedAt' => $item->updated_at->toISOString(),
            ]
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $item = MenuItem::where('id', $id)->where('owner_id', $user->id)->first();

        if (!$item) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Item not found']], 404);
        }

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully']);
    }
}
