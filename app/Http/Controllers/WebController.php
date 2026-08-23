<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Table;

class WebController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function dashboard()
    {
        return view('dashboard.index');
    }

    public function orders()
    {
        return view('dashboard.orders');
    }

    public function kds()
    {
        return view('dashboard.kds');
    }

    public function billing()
    {
        return view('dashboard.billing');
    }

    public function menu()
    {
        return view('dashboard.menu');
    }

    public function tables()
    {
        return view('dashboard.tables');
    }

    public function reports()
    {
        return view('dashboard.reports');
    }

    public function settings()
    {
        return view('dashboard.settings');
    }

    public function superadmin()
    {
        return view('superadmin.index');
    }

    public function superadminRestaurants()
    {
        return view('superadmin.restaurants');
    }

    // Hotel PMS Views
    public function hotelRooms()
    {
        return view('hotel.rooms');
    }

    public function hotelGuests()
    {
        return view('hotel.guests');
    }

    public function hotelFolio(string $bookingId)
    {
        return view('hotel.folio', ['bookingId' => $bookingId]);
    }

    public function hotelServices()
    {
        return view('hotel.services');
    }

    public function hotelPartner()
    {
        return view('hotel.partner');
    }

    // Customer Dining View
    public function customerOrder(string $ownerId, string $tableNumber)
    {
        return view('order.index', [
            'ownerId' => $ownerId,
            'tableNumber' => $tableNumber,
        ]);
    }

    public function customerOrderSuccess(string $ownerId, string $tableNumber)
    {
        return view('order.success', [
            'ownerId' => $ownerId,
            'tableNumber' => $tableNumber,
        ]);
    }

    // Customer In-Room Guest Portal View
    public function customerRoom(string $hotelId, string $roomNumber)
    {
        return view('room.index', [
            'hotelId' => $hotelId,
            'roomNumber' => $roomNumber,
        ]);
    }
}
