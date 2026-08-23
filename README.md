# Vyora — Unified Hospitality Operating System (Hotel PMS, Dining POS & Cross-Partner Network)

<p align="center">
  <img src="public/images/logo.png" width="160" alt="Vyora Logo" style="border-radius: 20px;">
</p>

<p align="center">
  <strong>The modern, unified operating platform for Hotel Property Management (PMS), In-Room QR Services, Restaurant POS, Kitchen KDS, and Cross-Business Dining Networks.</strong>
</p>

---

## 🌟 Core Modules

1. **🏨 Hotel Property Management (PMS)**
   * Live Color-Coded Room Grid (Clean 🟢, Turnover Dirty 🟠, Occupied 🔵, Maintenance 🔴).
   * 1-Tap Guest Check-In & Check-Out with ID capture.
   * Unified Guest Folio Invoicing (Room Tariff + In-House Food - Advance Deposit).
   * Housekeeping & Concierge live dispatch queue.

2. **📱 In-Room QR Guest Portal (`/room/{hotelId}/{roomNumber}`)**
   * Cryptographic HMAC-SHA256 authenticated guest sessions.
   * Tab 1: In-Room Dining Menu (with dietary instructions and live tracking).
   * Tab 2: Housekeeping & Amenities (Clean Room, Towels, Mineral Water, Toiletries).
   * Tab 3: Concierge & Front Desk (Wake-up Calls, Luggage Assistance, Taxi booking).

3. **🍽️ Restaurant POS & Table QR (`/order/{ownerId}/{tableNumber}`)**
   * Real-time Customer Mobile Menu.
   * Orders Kanban Board & Kitchen Display System (KDS).
   * Table Bill Settlement with thermal receipts.

4. **🤝 Cross-Business Partner Network (`/dashboard/hotel/partner`)**
   * Hotels can link nearby partner restaurants to fulfill room service food orders.
   * Restaurant kitchens receive tickets with full origin details: `🏨 [Hotel Name] • Room #[Number]`.

---

## 🚀 MilesWeb / cPanel Deployment Instructions

### 1. Database Configuration
In your MilesWeb cPanel / mPanel:
* **Database Name**: `arrwalle1_vyora`
* **Database User**: `arrwalle1_vyora`
* **Database Host**: `localhost` (or `127.0.0.1`)

Import the clean pre-seeded schema dump into phpMyAdmin:
* Upload and execute [`database/schema_mysql.sql`](database/schema_mysql.sql).

### 2. Configure `.env` on Server
Create or update your `.env` file in the root folder:

```env
APP_NAME=Vyora
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arrwalle1_vyora
DB_USERNAME=arrwalle1_vyora
DB_PASSWORD=YOUR_DB_PASSWORD

JWT_SECRET=your_super_secret_jwt_random_key_here

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
```

### 3. Permissions & Optimization
```bash
# Set folder permissions
chmod -R 775 storage bootstrap/cache

# Generate application key & optimize
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🧪 Default Demo Accounts

| Model | Username | Password | Role |
|---|---|---|---|
| 🏨 **Hotel PMS** | `grandhorizon` | `demo123` | Grand Horizon Hotel & Suites |
| 🍽️ **Restaurant POS** | `spicehub` | `demo123` | SpiceHub Dining |
| 🛡️ **Super Admin** | `superadmin` | `admin123` | Vyora Platform Admin |

---

## 📄 License
Vyora is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
