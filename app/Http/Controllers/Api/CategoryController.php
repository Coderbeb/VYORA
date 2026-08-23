<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuCategory;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');

        $categories = MenuCategory::where('owner_id', $user->id)
            ->withCount('items')
            ->orderBy('sort_order', 'asc')
            ->get();

        $formatted = $categories->map(function ($cat) {
            return [
                'id' => $cat->id,
                'ownerId' => $cat->owner_id,
                'name' => $cat->name,
                'description' => $cat->description,
                'sortOrder' => $cat->sort_order,
                '_count' => ['items' => $cat->items_count],
                'createdAt' => $cat->created_at->toISOString(),
                'updatedAt' => $cat->updated_at->toISOString(),
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $name = trim($request->input('name', ''));

        if (!$name) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'MISSING_FIELDS', 'message' => 'Category name is required']
            ], 400);
        }

        $maxSort = MenuCategory::where('owner_id', $user->id)->max('sort_order') ?? -1;

        $category = MenuCategory::create([
            'owner_id' => $user->id,
            'name' => $name,
            'description' => trim($request->input('description', '')) ?: null,
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'ownerId' => $category->owner_id,
                'name' => $category->name,
                'description' => $category->description,
                'sortOrder' => $category->sort_order,
                '_count' => ['items' => 0],
                'createdAt' => $category->created_at->toISOString(),
                'updatedAt' => $category->updated_at->toISOString(),
            ]
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $category = MenuCategory::where('id', $id)->where('owner_id', $user->id)->first();

        if (!$category) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Category not found']], 404);
        }

        if ($request->has('name')) $category->name = trim($request->input('name'));
        if ($request->has('description')) $category->description = trim($request->input('description')) ?: null;
        if ($request->has('sortOrder')) $category->sort_order = (int) $request->input('sortOrder');

        $category->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'ownerId' => $category->owner_id,
                'name' => $category->name,
                'description' => $category->description,
                'sortOrder' => $category->sort_order,
            ]
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $category = MenuCategory::where('id', $id)->where('owner_id', $user->id)->first();

        if (!$category) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Category not found']], 404);
        }

        $category->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }
}
