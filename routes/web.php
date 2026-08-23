<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;

// Public Pages
Route::get('/', [WebController::class, 'landing'])->name('landing');
Route::get('/auth/login', [WebController::class, 'login'])->name('login');
Route::get('/auth/register', [WebController::class, 'register'])->name('register');

// Customer Dining & Order Pages (Restaurant)
Route::get('/order/{ownerId}/{tableNumber}', [WebController::class, 'customerOrder'])->name('customer.order');
Route::get('/order/{ownerId}/{tableNumber}/success', [WebController::class, 'customerOrderSuccess'])->name('customer.success');

// Customer In-Room Guest Portal (Hotel)
Route::get('/room/{hotelId}/{roomNumber}', [WebController::class, 'customerRoom'])->name('customer.room');

// Management Dashboard (Adaptive for Restaurant, Hotel, Both)
Route::prefix('dashboard')->group(function () {
    Route::get('/', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [WebController::class, 'orders'])->name('dashboard.orders');
    Route::get('/kds', [WebController::class, 'kds'])->name('dashboard.kds');
    Route::get('/billing', [WebController::class, 'billing'])->name('dashboard.billing');
    Route::get('/menu', [WebController::class, 'menu'])->name('dashboard.menu');
    Route::get('/tables', [WebController::class, 'tables'])->name('dashboard.tables');
    Route::get('/reports', [WebController::class, 'reports'])->name('dashboard.reports');
    Route::get('/staff', [WebController::class, 'staff'])->name('dashboard.staff');
    Route::get('/settings', [WebController::class, 'settings'])->name('dashboard.settings');

    // Hotel PMS Specific Routes
    Route::prefix('hotel')->group(function () {
        Route::get('/rooms', [WebController::class, 'hotelRooms'])->name('hotel.rooms');
        Route::get('/guests', [WebController::class, 'hotelGuests'])->name('hotel.guests');
        Route::get('/folio/{bookingId}', [WebController::class, 'hotelFolio'])->name('hotel.folio');
        Route::get('/services', [WebController::class, 'hotelServices'])->name('hotel.services');
        Route::get('/partner', [WebController::class, 'hotelPartner'])->name('hotel.partner');
    });
});

// Super Admin Dashboard
Route::prefix('superadmin')->group(function () {
    Route::get('/', [WebController::class, 'superadmin'])->name('superadmin');
    Route::get('/restaurants', [WebController::class, 'superadminRestaurants'])->name('superadmin.restaurants');
    Route::get('/settings', [WebController::class, 'superadminSettings'])->name('superadmin.settings');
});
