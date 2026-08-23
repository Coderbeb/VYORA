<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SuperAdminController;
use App\Http\Controllers\Api\PublicMenuController;
use App\Http\Controllers\Api\PublicOrderController;
use App\Http\Controllers\Api\ServiceRequestController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\HotelRoomController;
use App\Http\Controllers\Api\HotelBookingController;
use App\Http\Controllers\Api\HotelFolioController;
use App\Http\Controllers\Api\HotelServiceController;
use App\Http\Controllers\Api\HotelPartnerController;
use App\Http\Controllers\Api\PublicRoomPortalController;
use App\Http\Controllers\Api\StaffController;

// Public Auth Endpoints
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:120,1');
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:30,1');

// Public Customer Dining Endpoints (Restaurant)
Route::get('/public/featured', [PublicMenuController::class, 'featured']);
Route::get('/public/menu', [PublicMenuController::class, 'index'])->middleware('throttle:120,1');
Route::get('/public/orders', [PublicOrderController::class, 'index'])->middleware('throttle:120,1');
Route::post('/public/orders', [PublicOrderController::class, 'store'])->middleware('throttle:60,1');
Route::post('/service-request', [ServiceRequestController::class, 'store'])->middleware('throttle:60,1');

// Public In-Room Guest Portal Endpoints (Hotel)
Route::get('/public/room/info', [PublicRoomPortalController::class, 'info'])->middleware('throttle:60,1');
Route::post('/public/room/orders', [PublicRoomPortalController::class, 'placeOrder'])->middleware('throttle:10,1');
Route::post('/public/room/services', [PublicRoomPortalController::class, 'requestService'])->middleware('throttle:10,1');
Route::get('/public/room/orders', [PublicRoomPortalController::class, 'liveOrders'])->middleware('throttle:60,1');

// Protected Routes for Restaurant & Hotel Owners, Staff, Admins
Route::middleware(['jwt.auth'])->group(function () {
    // Auth profile & password
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::get('/auth/verify', [AuthController::class, 'verify']);

    // Dashboard Overview
    Route::get('/stats', [DashboardController::class, 'stats']);

    // Orders & Operations
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);

    // Table Billing & Settlement
    Route::get('/billing', [BillingController::class, 'index']);
    Route::put('/billing', [BillingController::class, 'settle']);

    // Menu Management
    Route::get('/menu', [MenuController::class, 'index']);
    Route::post('/menu', [MenuController::class, 'store']);
    Route::put('/menu/{id}', [MenuController::class, 'update']);
    Route::delete('/menu/{id}', [MenuController::class, 'destroy']);

    // Menu Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Tables & QR
    Route::get('/tables', [TableController::class, 'index']);
    Route::post('/tables', [TableController::class, 'store']);
    Route::put('/tables/{id}', [TableController::class, 'update']);
    Route::delete('/tables/{id}', [TableController::class, 'destroy']);

    // Hotel PMS & Room Management Endpoints
    Route::prefix('hotel')->group(function () {
        Route::get('/categories', [HotelRoomController::class, 'categories']);
        Route::post('/categories', [HotelRoomController::class, 'storeCategory']);
        Route::delete('/categories/{id}', [HotelRoomController::class, 'destroyCategory']);

        Route::get('/rooms', [HotelRoomController::class, 'index']);
        Route::post('/rooms', [HotelRoomController::class, 'store']);
        Route::put('/rooms/{id}', [HotelRoomController::class, 'update']);
        Route::delete('/rooms/{id}', [HotelRoomController::class, 'destroy']);

        Route::get('/bookings', [HotelBookingController::class, 'index']);
        Route::post('/check-in', [HotelBookingController::class, 'checkIn']);
        Route::post('/check-out', [HotelBookingController::class, 'checkOut']);

        Route::get('/folio/{bookingId}', [HotelFolioController::class, 'show']);

        Route::get('/services', [HotelServiceController::class, 'index']);
        Route::put('/services/{id}', [HotelServiceController::class, 'update']);

        Route::get('/partner', [HotelPartnerController::class, 'index']);
        Route::post('/partner', [HotelPartnerController::class, 'togglePartner']);
    });

    // Staff & Employee Management (Front Desk, Housekeeping, Chef, Waiter, Cashier, Manager)
    Route::get('/staff', [StaffController::class, 'index']);
    Route::post('/staff', [StaffController::class, 'store']);
    Route::put('/staff/{id}', [StaffController::class, 'update']);
    Route::delete('/staff/{id}', [StaffController::class, 'destroy']);

    // Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index']);

    // File uploads
    Route::post('/upload', [UploadController::class, 'store']);

    // Super Admin Area
    Route::middleware(['superadmin'])->prefix('superadmin')->group(function () {
        Route::get('/stats', [SuperAdminController::class, 'stats']);
        Route::get('/restaurants', [SuperAdminController::class, 'restaurants']);
        Route::post('/tenants', [SuperAdminController::class, 'createTenant']);
        Route::put('/restaurants', [SuperAdminController::class, 'updateRestaurant']);
        Route::delete('/restaurants/{id}', [SuperAdminController::class, 'deleteRestaurant']);

        // Platform Customization Settings
        Route::get('/settings', [SuperAdminController::class, 'getSettings']);
        Route::post('/settings', [SuperAdminController::class, 'updateSettings']);

        // SaaS Subscription Plans
        Route::get('/plans', [SuperAdminController::class, 'getPlans']);
        Route::post('/plans', [SuperAdminController::class, 'savePlan']);
        Route::delete('/plans/{id}', [SuperAdminController::class, 'deletePlan']);
    });
});
