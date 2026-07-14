# iRecovery — Master Development Prompt

Use this prompt verbatim when working with any AI coding assistant on this project.

---

## Project Overview

**iRecovery** is a PHP/MySQL web platform built on XAMPP that helps people in Uganda (and beyond) recover lost documents — National IDs, Driving Permits, Passports, Student IDs, Academic Documents, Land Titles, Birth Certificates, and other official documents.

The system is already partially built. The stack is:
- **Backend:** PHP (procedural, no framework), MySQLi with prepared statements
- **Frontend:** Bootstrap 5.3, Bootstrap Icons, Inter font (Google Fonts)
- **Database:** MySQL/MariaDB (`u850523537_iRecoverDB`)
- **Server:** XAMPP (Apache + PHP + MySQL), Windows
- **Sessions:** PHP native sessions, role-separated session keys

---

## Directory Structure

```
irecover/
├── index.php             ← Public homepage (upload found, report lost, search)
├── login.php             ← Station login  → $_SESSION['station_user']
├── adminlogin.php        ← Super Admin / Admin login → $_SESSION['admin_user']
├── submit_id.php         ← Handles Upload Found Document form POST
├── report.php            ← Handles Report Lost Document form POST
├── search_id.php         ← Handles document search POST
├── db.php                ← Database connection (MySQLi)
├── db.php                ← DB: DB_NAME = u850523537_iRecoverDB
├── admin/
│   ├── index.php         ← Super Admin / Admin dashboard
│   ├── user_saver.php    ← AJAX endpoint: add station
│   └── logout.php
├── station/
│   ├── index.php         ← Station dashboard
│   └── logout.php
├── uploads/              ← Document images
└── database/
    ├── u850523537_iRecoverDB.sql    ← Original schema + data
    └── irecover_migration_v2.sql   ← v2 migration (run this next)
```

---

## User Roles & Access Levels

| Role | Session Key | Login Page | Dashboard |
|---|---|---|---|
| **Super Admin** | `$_SESSION['admin_user']` | `adminlogin.php` | `admin/index.php` |
| **Admin** | `$_SESSION['admin_user']` | `adminlogin.php` | `admin/index.php` |
| **Station** | `$_SESSION['station_user']` | `login.php` | `station/index.php` |
| **Public** | none | none | `index.php` |

Role is stored in `admins.role` = `super_admin` | `admin` | `station`.

### Login Credentials (after running migration v2)

| Username | Password | Role |
|---|---|---|
| `superadmin` | `SuperAdmin@2025` | super_admin |
| `admin` | `Admin@2025` | admin |
| `Voice of Lango FM` | `123` | station |
| `Qfm` | `123` | station |
| `Voice of The Gospel` | `123` | station |
| `Lira Central Police` | `Lira@2025` | station |

---

Username	Password	Role
superadmin	SuperAdmin@2025	Full system access
admin	Admin@2025	Manage documents, alerts
Voice of Lango FM	123	Station
Lira Central Police	Lira@2025	Station


## Complete System Flow

### 1. Station uploads a found document
- Station logs in → `station/index.php`
- Clicks "Upload Found Document" → redirects to `index.php` (Upload Found form)
- Fills: Document Type, owner details, front/back photos
- Form POSTs to `submit_id.php` → inserts into `documents` table with `action='found'`, `station_holding = station_username`
- On insert: system auto-checks `lost_reports` for a match (same `id_number` OR same `sur_name` + `given_name` + `dob`)
- If match found → insert row into `match_alerts` + `notifications` for admin

### 2. Public reports a lost document
- Visitor on `index.php` → "Report Lost Document" accordion
- Fills: Document Type, owner details, police letter upload, reporter phone
- Form POSTs to `report.php` → inserts into `lost_reports` with `match_status='unmatched'`
- System auto-checks `documents` table for a match
- If match found → insert into `match_alerts` + `notifications`

### 3. Public searches for their document
- Visitor on `index.php` → "Search Found Documents" accordion
- Search by: NIN/ID number (primary) OR surname + given name + date of birth (fuzzy fallback)
- Form POSTs to `search_id.php`
- If match found:
  - Show document preview (front image), station contact info
  - Log search in `search_log` with searcher's phone
  - Insert notification: admin sees search match + searcher phone
- If no match: show "not found" message

### 4. Admin sees the match alert
- Admin logs in → `admin/index.php`
- Dashboard shows notification badges: new match alerts
- Admin reviews match: lost report details vs. found document details
- Admin contacts station holding the document
- Admin updates `match_alerts.alert_status` = `'owner_notified'`

### 5. Payment
- Owner is told to pay via Mobile Money
- On `search_id.php` result page: "Pay to Recover" button appears when matched
- Owner enters their NIN/phone + clicks pay
- System creates row in `payments` table (`status='initiated'`)
- Payment instruction: send UGX [fee] to [mobile money number], use NIN as reference
- Admin/station manually confirms payment → updates `payments.status='confirmed'`
- Payment reflected on station dashboard and admin dashboard
- `match_alerts.alert_status` updates to `'paid'`

### 6. Collection at station
- Owner visits the station physically
- Station verifies identity vs. document details
- Station marks `collection_log` entry + sets `documents.action='collected'`
- `match_alerts.alert_status` = `'collected'`
- Both admin and station dashboard update counts

---

## Database Tables (v2)

### Core tables to use for all new code:
- `admins` — all users (super_admin, admin, station). Has `role` column.
- `documents` — all found documents (unified, replaces national_ids etc.)
- `lost_reports` — all reported lost documents
- `match_alerts` — system-generated match notifications
- `payments` — mobile money payment records
- `collection_log` — when document physically picked up
- `notifications` — in-app alerts for admins/stations
- `search_log` — every public search attempt
- `fee_config` — per-document-type recovery fees

### Legacy tables (keep, read-only migration):
- `national_ids`, `driving_permits`, `student_ids` — historical data only

---

## Document Types Supported

```
national_id        → NIN number, surname, given name, DOB, gender
driving_permit     → Permit number, NIN, surname, given name, DOB
passport           → Passport number, surname, given name, DOB, nationality
student_id         → Student number, school, course, date issued, name
academic_document  → Institution, course title, graduation year, name
land_title         → Plot number / land reference, owner name, district
birth_certificate  → Registration number, name, DOB, district
other              → Free description
```

---

## Coding Standards for This Project

1. **All SQL via prepared statements** — `$conn->prepare()` + `bind_param()`. No string interpolation in queries.
2. **Session keys**: `$_SESSION['admin_user']` for super_admin/admin, `$_SESSION['station_user']` for stations.
3. **Auth guards on every protected page**:
   - Admin pages: `if (!isset($_SESSION['admin_user'])) { header('Location: ../adminlogin.php'); exit(); }`
   - Station pages: `if (!isset($_SESSION['station_user'])) { header('Location: ../login.php'); exit(); }`
4. **Role check for super_admin-only actions** (add stations, view all data, change fees):
   ```php
   // In admin/index.php after auth guard:
   $stmt = $conn->prepare("SELECT role FROM admins WHERE user_name=? LIMIT 1");
   $stmt->bind_param('s', $_SESSION['admin_user']);
   $stmt->execute();
   $role = $stmt->get_result()->fetch_assoc()['role'] ?? 'admin';
   $isSuperAdmin = ($role === 'super_admin');
   ```
5. **Uploads**: save to `uploads/` directory. Filename format: `TYPE_RAND_TIMESTAMP.png`
6. **Passwords**: currently plaintext (legacy). For new users use `password_hash()` / `password_verify()`. Do not break existing stations.
7. **Primary color**: `#CC0000` (red). Dark mode dashboards use `rgba(30,30,30,0.95)` backgrounds.
8. **Bootstrap version**: 5.3.0 — do not upgrade without asking.
9. **No JavaScript frameworks** — vanilla JS only.
10. **File encoding**: UTF-8. All PHP files start with `<?php` (no BOM).

---

## What Still Needs to Be Built

- [ ] Auto-match logic on `submit_id.php` and `report.php` (query lost_reports / documents on insert)
- [ ] `match_alerts` display panel in `admin/index.php`
- [ ] Notification badge / bell in admin and station headers
- [ ] Payment initiation page (`pay.php`) — enter NIN, show fee, generate payment instructions
- [ ] Payment confirmation by admin/station
- [ ] Collection confirmation form in `station/index.php`
- [ ] Full unified document upload form (all doc types in `documents` table)
- [ ] Full lost report form with police letter upload (`lost_reports` table)
- [ ] Search using NIN **OR** name + DOB fallback
- [ ] `search_log` insert on every search
- [ ] Admin notification panel showing new matches + searcher contact
- [ ] Role-based UI differences: super_admin sees "Add Station" + fees config; admin does not
- [ ] Fee config management page (super_admin only)
- [ ] Passport, Land Title, Birth Certificate form fields

---

## Important Notes

- The `admins` table serves ALL authenticated users (super_admin, admin, station).  
  The `superadmins` table is legacy — do not write to it anymore.
- `documents.reporter` stores the station username (or `'Public'` if submitted publicly).
- `documents.station_holding` stores where the physical document is kept (may differ from reporter if transferred).
- Mobile money payment is manual-confirm for now — no live payment API integrated yet.
- The site is deployed at `https://id.faithfellows.online/` in production; locally at `http://localhost/irecover/`.
- Existing data in `national_ids`, `driving_permits`, `student_ids` must remain accessible and display in dashboards.
