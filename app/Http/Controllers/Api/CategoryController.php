<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuCategory;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
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
        $user = $request->attributes->get('auth_user');
        $ownerId = $this->resolveOwnerId($user);

        if (!$ownerId) {
            return response()->json(['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Authentication required']], 401);
        }

        $categories = MenuCategory::where('owner_id', $ownerId)
            ->withCount('items')
            ->orderBy('sort_order', 'asc')
            ->get();

        $formatted = $categories->map(function ($cat) {
            return [
                'id' => $cat->id,
                'ownerId' => $cat->owner_id,
                'name' => $cat->name,
                'description' => $cat->description,
                'sortOrder' => (int) $cat->sort_order,
                '_count' => ['items' => (int) $cat->items_count],
                'createdAt' => $this->formatDate($cat->created_at),
                'updatedAt' => $this->formatDate($cat->updated_at),
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $this->resolveOwnerId($user);

        if (!$ownerId) {
            return response()->json(['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Authentication required']], 401);
        }

        $name = trim((string) $request->input('name', ''));
        if ($name === '') {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'MISSING_FIELDS', 'message' => 'Category name is required']
            ], 400);
        }

        try {
            $maxSort = MenuCategory::where('owner_id', $ownerId)->max('sort_order') ?? -1;

            $category = MenuCategory::create([
                'owner_id' => $ownerId,
                'name' => $name,
                'description' => trim((string) $request->input('description', '')) ?: null,
                'sort_order' => $maxSort + 1,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'ownerId' => $category->owner_id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'sortOrder' => (int) $category->sort_order,
                    '_count' => ['items' => 0],
                    'createdAt' => $this->formatDate($category->created_at),
                    'updatedAt' => $this->formatDate($category->updated_at),
                ],
                'message' => 'Category created successfully'
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Category create failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DATABASE_ERROR', 'message' => 'Failed to create category: ' . $e->getMessage()]
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

        $category = MenuCategory::where('id', $id)->where('owner_id', $ownerId)->first();

        if (!$category) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Category not found']], 404);
        }

        try {
            if ($request->has('name')) {
                $newName = trim((string) $request->input('name'));
                if ($newName !== '') {
                    $category->name = $newName;
                }
            }
            if ($request->has('description')) {
                $category->description = trim((string) $request->input('description')) ?: null;
            }
            if ($request->has('sortOrder')) {
                $category->sort_order = (int) $request->input('sortOrder');
            }

            $category->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'ownerId' => $category->owner_id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'sortOrder' => (int) $category->sort_order,
                    'createdAt' => $this->formatDate($category->created_at),
                    'updatedAt' => $this->formatDate($category->updated_at),
                ],
                'message' => 'Category updated successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DATABASE_ERROR', 'message' => 'Failed to update category: ' . $e->getMessage()]
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

        $category = MenuCategory::where('id', $id)->where('owner_id', $ownerId)->first();

        if (!$category) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Category not found']], 404);
        }

        try {
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
        } catch (\Throwable $e) {
            Log::error('Category delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DATABASE_ERROR', 'message' => 'Failed to delete category: ' . $e->getMessage()]
            ], 500);
        }
    }
}
