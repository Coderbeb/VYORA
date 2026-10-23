<!-- BEGIN:nextjs-agent-rules -->
# This is NOT the Next.js you know

This version has breaking changes — APIs, conventions, and file structure may all differ from your training data. Read the relevant guide in `node_modules/next/dist/docs/` before writing any code. Heed deprecation notices.
<!-- END:nextjs-agent-rules -->

# Vyora — Agent Guidelines & System Architecture

This document is the canonical reference and behavioral guide for all AI agents, engineers, and contributors working on the **Vyora** codebase.

---

## 1. Product Identity & Branding Rules

- **Official Product Name**: **Vyora** (Unified Hospitality Operating System).
- **Naming Enforcement**: Always refer to the system, company, and platform as **Vyora**. **Never** use *QRestro* or *QRBite* in any user-facing strings, logs, page titles, documentation, commit messages, or comments.
- **Core Positioning**: A modern, unified operating system combining Hotel Property Management (PMS), In-Room QR Services, Restaurant POS, Kitchen KDS, Staff Role-Based Access Control (RBAC), Cross-Business Dining Networks, and a White-Label Multi-Tenant SaaS platform.

---

## 2. Active Tech Stack

The production application is a unified **PHP / Laravel** full-stack system with modern asset bundling:

| Component | Technology | Version | Purpose |
|---|---|---|---|
| **Backend Framework** | Laravel Framework | 11.x / 13.x (`^13.17`) | API endpoints, business logic, ORM, middleware, routing |
| **Language Runtime** | PHP | `^8.3` | Modern typed PHP with strict typing |
| **View Engine** | Blade Templates | Laravel Blade | Server-rendered responsive UI views |
| **Frontend Styling** | Tailwind CSS v4 + Apple HIG CSS Variables | `^4.0.0` | Utility styling + custom CSS variable design system |
| **Build & Dev Tool** | Vite + Laravel Vite Plugin | Vite `^8.0`, Plugin `^3.1` | Hot module replacement, asset compilation |
| **Database** | SQLite (dev) / MySQL (prod) | SQLite 3 / MySQL 8 | Relational data persistence with Eloquent ORM |
| **Authentication** | Firebase PHP-JWT | `^7.1` | Stateless JWT verification for web, API & staff sessions |
| **QR Code Engine** | BaconQrCode | `^3.1` | Cryptographically signed SVG/PNG QR generation |
| **Real-Time Broadcast** | Pusher PHP Server & WebSockets | `^7.3` | Live event dispatching (orders, KDS, waiter calls) |
| **Legacy Note** | Next.js / TypeScript files | — | Historical prototype remnants; active runtime is 100% PHP/Laravel |

---

## 3. Project Architecture & Directory Map

```
j:\Qrestro/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                     # 20 RESTful API Controllers
│   │   │   │   ├── AuthController.php            # Login, registration, JWT profile
│   │   │   │   ├── BillingController.php         # Table billing & settlement
│   │   │   │   ├── CategoryController.php        # Dish menu categories
│   │   │   │   ├── DashboardController.php       # Real-time venue metrics & stats
│   │   │   │   ├── HotelBookingController.php    # Check-in, check-out, bookings
│   │   │   │   ├── HotelFolioController.php      # Consolidated guest folios
│   │   │   │   ├── HotelPartnerController.php    # Cross-business restaurant linking
│   │   │   │   ├── HotelRoomController.php       # Room inventory & categories
│   │   │   │   ├── HotelServiceController.php    # Housekeeping & concierge dispatch
│   │   │   │   ├── MenuController.php            # CRUD for restaurant dishes
│   │   │   │   ├── OrderController.php           # Order status management (KDS)
│   │   │   │   ├── PublicMenuController.php      # Guest table menu + HMAC auth
│   │   │   │   ├── PublicOrderController.php     # Customer order placement
│   │   │   │   ├── PublicRoomPortalController.php# Hotel guest in-room portal & dining
│   │   │   │   ├── ReportController.php          # Revenue & sales analytics
│   │   │   │   ├── ServiceRequestController.php  # Waiter/table assistance calls
│   │   │   │   ├── StaffController.php           # Employee RBAC & PINs
│   │   │   │   ├── SuperAdminController.php      # SaaS platform tenants & settings
│   │   │   │   ├── TableController.php           # Tables & QR code generation
│   │   │   │   └── UploadController.php          # Image uploads for menu items
│   │   │   └── WebController.php        # Route handler for all Blade views
│   │   └── Middleware/
│   │       ├── JwtAuthMiddleware.php    # Bearer header, cookie & session JWT validation
│   │       └── SuperAdminMiddleware.php # Restricts superadmin routes to SUPER_ADMIN role
│   ├── Models/                          # 15 Eloquent Models
│   │   ├── Booking.php                  # Hotel stay, check-in, check-out, room rate, deposit
│   │   ├── Guest.php                    # Guest directory, phone, ID credentials
│   │   ├── HotelServiceRequest.php      # Housekeeping, concierge & maintenance tickets
│   │   ├── MenuCategory.php             # Starters, Mains, Desserts, Drinks
│   │   ├── MenuItem.php                 # Dish details, price, prep time, availability
│   │   ├── Order.php                    # Dine-in & room-service orders with lifecycle state
│   │   ├── OrderItem.php                # Line items per order
│   │   ├── Owner.php                    # Multi-tenant venue account (Restaurant / Hotel / Both)
│   │   ├── PlatformSetting.php          # White-label SaaS configuration (brand, currency, etc.)
│   │   ├── Room.php                     # Hotel rooms, status, floor, signed QR code
│   │   ├── RoomCategory.php             # Deluxe, Suite, Executive, amenities, base price
│   │   ├── StaffMember.php              # Employees with roles (Waiter, Chef, Front Desk, etc.)
│   │   ├── SubscriptionPlan.php         # SaaS tiers (Free, Pro, Premium)
│   │   ├── Table.php                    # Dining tables with signed QR URLs
│   │   └── User.php                     # Base user model
│   └── Services/
│       ├── BroadcastService.php         # Pusher WebSocket dispatcher for live updates
│       ├── QRService.php                # BaconQrCode SVG generator & signed URL builder
│       ├── SecurityService.php          # JWT handling, HMAC signatures & bcrypt hashing
│       └── StorageService.php           # File & image upload management
├── database/
│   ├── migrations/                      # Database schema definitions
│   ├── seeders/DatabaseSeeder.php       # Complete demo seed dataset
│   └── database.sqlite                  # Local SQLite database file
├── public/
│   ├── css/globals.css                  # Apple HIG Design System CSS variables
│   ├── images/                          # Brand assets & placeholders
│   └── uploads/                         # Media file storage
├── resources/
│   ├── css/app.css                      # Tailwind CSS v4 entry point
│   ├── js/app.js                        # Frontend JS entry point
│   └── views/
│       ├── auth/                        # Login and registration views
│       ├── dashboard/                   # Orders, KDS, Menu, Tables, Staff, Billing, Reports
│       ├── hotel/                       # Rooms, Guests, Folio, Services, Partner Network
│       ├── layouts/                     # Master layout templates (app, dashboard, superadmin)
│       ├── order/                       # Customer dining table ordering portal
│       ├── room/                        # Customer hotel room portal
│       ├── superadmin/                  # Multi-tenant oversight, branding & settings
│       └── landing.blade.php            # Public Vyora platform landing page
└── routes/
    ├── api.php                          # RESTful API route definitions
    └── web.php                          # Web page & Blade view routes
```

---

## 4. Key Functional Modules

### 1. Hotel Property Management (PMS)
- **Room Status Flow**: `vacant_clean` 🟢 ➔ `occupied` 🔵 ➔ `vacant_dirty` 🟠 ➔ `maintenance` 🔴.
- **Guest Folio Settlement**: Merges room tariff, in-room dining orders, and housekeeping amenities into a single itemized guest bill (`/dashboard/hotel/folio/{bookingId}`).
- **Housekeeping & Concierge Dispatch**: Real-time service board (`hotel_service_requests`) for towels, toiletries, room cleaning, wake-up calls, and luggage assistance.

### 2. In-Room QR Portal (`/room/{hotelId}/{roomNumber}`)
- **HMAC Tamper-Proof Authentication**: Signed QR URLs contain an HMAC-SHA256 signature (`?code=...`) verified by `SecurityService::getRoomSignature()`.
- **Zero App Download**: Guests access dining menus, request amenities, and track live order progress directly from any browser.

### 3. Restaurant POS & Table QR Ordering (`/order/{ownerId}/{tableNumber}`)
- **Table QR Signing**: Tables have unique HMAC signatures preventing guest spoofing or order falsification.
- **Session Billing**: Multiple order rounds at the same table consolidate into a single dining session.
- **Printable Thermal Receipts**: Clean 80mm/58mm thermal receipt layout with taxes, subtotals, and totals.

### 4. Kitchen Display System (KDS) (`/dashboard/kds`)
- **Kanban Pipeline**: `pending` ➔ `preparing` ➔ `ready` ➔ `completed` (or `cancelled`).
- **Real-Time Push**: WebSockets broadcast order events (`order:new`, `order:updated`) directly to kitchen screens.

### 5. Cross-Business Multi-Partner Network (`/dashboard/hotel/partner`)
- Hotels without kitchens link to partner restaurants in the network.
- Orders route automatically to partner kitchens tagged with origin: `🏨 [Hotel Name] • Room #[Number]`.
- Folios automatically aggregate partner restaurant charges into guest room bills.

### 6. Team & Staff Role-Based Access Control (RBAC) (`/dashboard/staff`)
- **Roles**: `FRONT_DESK`, `HOUSEKEEPING`, `CHEF`, `WAITER`, `CASHIER`, `MANAGER`.
- **Fast Switch**: Quick 4-digit employee PINs for shared terminal workstations.

### 7. Super Admin White-Label SaaS Platform (`/superadmin`)
- White-label customization: Platform name, tagline, branding accents, default currencies (INR `₹`, USD `$`, EUR `€`), and support channels.
- Multi-tenant subscription builder with room, table, and feature quotas.

---

## 5. Design System & Frontend Guidelines

All interfaces adhere to Apple Human Interface Guidelines (HIG) with modern luxury aesthetics:

### Default Theme: Light Mode
- **Light mode is the default**. Never set dark theme as the default stylesheet root.
- Dark theme is strictly an override activated via `[data-theme="dark"]`.

### Core Design Tokens (from `globals.css`)
- **Background Base**: Warm Stone Chalk (`--bg-base: #fbfbfa` / dark `#0a0f0d`).
- **Surfaces**: Pure Alabaster (`--bg-surface: #ffffff` / dark `#111a17`).
- **Primary Accent**: Emerald Forest Green (`--accent: #034d37` / dark Champagne Gold `#c5a880`).
- **Secondary Accent**: Champagne Gold (`--accent-2: #c5a880` / dark Forest Green `#056e4e`).
- **Typography**:
  - Headings & Editorial Branding: `Cormorant Garamond` (serif).
  - UI, Forms & Data Tables: `Plus Jakarta Sans` (sans-serif).
- **Apple Squircle Radii**:
  - Small elements (badges, small inputs): `--radius-sm: 8px`
  - Medium elements (cards, buttons): `--radius-md: 12px`
  - Large elements (containers, modals): `--radius-lg: 16px`
  - Extra-large layouts: `--radius-xl: 22px`
- **Fluid Micro-Animations**:
  - Default transition: `--transition: 0.25s cubic-bezier(0.16, 1, 0.3, 1)`
  - Spring interaction: `--transition-spring: 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)`

### Responsiveness & Accessibility
- **Mobile-First Layouts**: Always write layout styles so they stack or form 2-column mobile grids by default, expanding on desktop viewports (`@media (min-width: 768px)`).
- **Touch Target Size**: Interactive elements (buttons, links, inputs, icons) must have a touch target of at least **44px**.
- **No Horizontal Overflow**: Tables and grids must wrap in `overflow-x: auto` containers. Never allow horizontal scroll on `<body>` or `<html>`.

---

## 6. Coding & Architectural Conventions

### PHP & Laravel Standards
- Follow **PSR-12** coding standards.
- Use explicit types for function parameters, return types, and class properties wherever possible.
- Eloquent models must define `$fillable`, proper relationships, and appropriate casts (`json`, `decimal:2`, `boolean`).
- Return clean, uniform JSON API envelopes:
  - Success: `response()->json(['success' => true, 'data' => $data, 'message' => '...'])`
  - Error: `response()->json(['success' => false, 'error' => ['code' => '...', 'message' => '...']], $status)`

### Security Protocols
- **Passwords & PINs**: Always hash with `SecurityService::hashPassword()` (bcrypt).
- **Session Tokens**: Table & Room guest ordering tokens expire in 12 hours and bind to table/room update timestamps.
- **HMAC Signatures**: Always verify HMAC signatures on incoming public order requests before processing.
- **Rate Limiting**: Public API routes must enforce throttling (`throttle:60,1` or `throttle:10,1`).

---

## 7. Developer & Verification Commands

```powershell
# Start local PHP server
php artisan serve

# Compile & run frontend dev assets (Tailwind CSS v4 + Vite)
npm run dev

# Build production assets
npm run build

# Run database migrations
php artisan migrate

# Seed demo data (SuperAdmin, SpiceHub Restaurant, Grand Horizon Hotel, Staff)
php artisan db:seed

# Run end-to-end test verification suites
php verify_all.php
php verify_hotel_and_all.php
```

---

## 8. Credentials for Local Testing

| Role | Username | Password / PIN | Description |
|---|---|---|---|
| **Super Admin** | `superadmin` | `admin123` | Master platform administrator |
| **Restaurant Owner** | `spicehub` | `demo123` | Demo restaurant with tables & active menu |
| **Hotel Owner** | `grandhorizon` | `demo123` | Demo hotel PMS with rooms, bookings & partner kitchen |
| **Default Staff** | *(Per venue)* | `1234` | Front Desk, Chef, Waiter, Housekeeping PINs |
