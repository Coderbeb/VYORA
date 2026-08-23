<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Owner;
use App\Services\QRService;
use App\Services\SecurityService;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $tables = Table::where('owner_id', $user->id)
            ->orderBy('table_number', 'asc')
            ->get();

        $requestHost = $request->header('host');

        // Auto-repair missing or invalid QR data
        foreach ($tables as $table) {
            $isJson = str_starts_with($table->qr_code_data ?? '', '{') || str_starts_with($table->qr_code_data ?? '', '[');
            $hasValidSig = false;

            if ($table->qr_code_data && !$isJson) {
                $query = parse_url($table->qr_code_data, PHP_URL_QUERY);
                parse_str($query ?? '', $params);
                if (isset($params['code']) && $params['code'] === SecurityService::getTableSignature($user->id, $table->table_number)) {
                    $hasValidSig = true;
                }
            }

            if (!$table->qr_code_data || $isJson || !$table->qr_code_image_url || !$hasValidSig) {
                $orderUrl = QRService::buildOrderUrl($user->id, $table->table_number, $requestHost);
                $qrDataUrl = QRService::generateQRCodeDataURL($orderUrl);

                $table->qr_code_data = $orderUrl;
                $table->qr_code_image_url = $qrDataUrl;
                $table->save();
            }
        }

        $formatted = $tables->map(function ($table) {
            return [
                'id' => $table->id,
                'ownerId' => $table->owner_id,
                'tableNumber' => $table->table_number,
                'qrCodeData' => $table->qr_code_data,
                'qrCodeImageUrl' => $table->qr_code_image_url,
                'isActive' => (bool) $table->is_active,
                'createdAt' => $table->created_at->toISOString(),
                'updatedAt' => $table->updated_at->toISOString(),
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $tableNumber = (int) $request->input('tableNumber');

        if ($tableNumber < 1) {
            return response()->json(['success' => false, 'error' => ['code' => 'INVALID_TABLE', 'message' => 'Valid table number is required']], 400);
        }

        $existing = Table::where('owner_id', $user->id)->where('table_number', $tableNumber)->first();
        if ($existing) {
            return response()->json(['success' => false, 'error' => ['code' => 'DUPLICATE', 'message' => 'Table number already exists']], 409);
        }

        // Check subscription tier limit
        $owner = Owner::with('plan')->find($user->id);
        $currentCount = Table::where('owner_id', $user->id)->count();
        $maxAllowed = $owner->plan?->max_tables ?? 5;

        if ($currentCount >= $maxAllowed) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PLAN_LIMIT_REACHED',
                    'message' => "Table limit reached ({$maxAllowed} tables maximum). Please upgrade your subscription plan."
                ]
            ], 403);
        }

        $requestHost = $request->header('host');
        $orderUrl = QRService::buildOrderUrl($user->id, $tableNumber, $requestHost);
        $qrDataUrl = QRService::generateQRCodeDataURL($orderUrl);

        $table = Table::create([
            'owner_id' => $user->id,
            'table_number' => $tableNumber,
            'qr_code_data' => $orderUrl,
            'qr_code_image_url' => $qrDataUrl,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $table->id,
                'ownerId' => $table->owner_id,
                'tableNumber' => $table->table_number,
                'qrCodeData' => $table->qr_code_data,
                'qrCodeImageUrl' => $table->qr_code_image_url,
                'isActive' => (bool) $table->is_active,
                'createdAt' => $table->created_at->toISOString(),
                'updatedAt' => $table->updated_at->toISOString(),
            ]
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $table = Table::where('id', $id)->where('owner_id', $user->id)->first();

        if (!$table) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Table not found']], 404);
        }

        if ($request->has('isActive')) {
            $table->is_active = (bool) $request->input('isActive');
        }

        $table->save();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $table->id,
                'ownerId' => $table->owner_id,
                'tableNumber' => $table->table_number,
                'qrCodeData' => $table->qr_code_data,
                'qrCodeImageUrl' => $table->qr_code_image_url,
                'isActive' => (bool) $table->is_active,
            ]
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $table = Table::where('id', $id)->where('owner_id', $user->id)->first();

        if (!$table) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Table not found']], 404);
        }

        $table->delete();
        return response()->json(['success' => true, 'message' => 'Table deleted successfully']);
    }
}
