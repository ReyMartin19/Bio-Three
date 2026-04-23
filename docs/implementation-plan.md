# DTR System Implementation Plan

## Overview
This document outlines the implementation plan for the DepEd CAR Attendance Monitoring System based on the provided requirements and database schema extensions. The architecture has been specifically tailored to follow the **Laravel Boost Guidelines**, utilizing Laravel 13, Vue 3, Inertia.js v3, and Pest PHP.

## Database Architecture (Laravel Migrations & Models)

Instead of executing raw SQL, all schema changes will be managed via Laravel Migrations (`php artisan make:migration`). Models will map to legacy tables using the `protected $table` property where necessary to maintain the `_ext` suffix convention.

### 1. Organizational Hierarchy Extensions
- **EmploymentTypeExt**: Stores employment categories (Job Order, Contract of Service, Plantilla).
- **UnitExt**: Defines organizational units within departments.
- **PositionExt**: Defines positions within units.
- **UserInfoExt**: Extends the legacy `Userinfo` with hierarchy relationships and a soft "Disable" status.

### 2. Work Arrangement Management
- **WorkArrangementExt**: Manages employee schedules (Full Flexi, Fixed Flexi, WFH), including covered periods and preferred WFH days.

### 3. Attendance Rules & Summaries
- **AttendanceRuleExt**: Seeded via a Laravel Database Seeder, storing configurable constants (e.g., 15-min grace period).
- **CheckInOutExt**: Tracks biometric sync status in real-time.
- **AttendanceSummary**: A new indexed table that stores daily computed status (Present, Late, Undertime, Absent, total hours) for faster dashboard queries ("Compute and Persist" strategy).

### 4. Calendar & Notifications
- **HolidayExt**: Categorizes holidays and work suspension status.
- **NotificationExt**: Manages system alerts and notifications.

## Core Business Logic (Laravel Domain Driven)

### 1. Attendance Rule Engine (`AttendanceService`)
All complex attendance rules will be encapsulated in a dedicated `App\Services\AttendanceService` class instead of inflating models or controllers.

#### General Rules
- Minimum 8 work hours per day (excluding lunch break).
- Flexitime window: 7:00 AM - 6:00 PM.
- Monday flag ceremony: All employees must time in before 8:00 AM.
- Duplicate punches: First time-in, last time-out are considered official.

#### Work Arrangement Specific Rules
- **Full Flexi Time**: Time-in (7:00-9:00 AM), Time-out (4:00-6:00 PM), Late limit (9:15 AM).
- **Fixed Flexi Time**:
  - Schedule A (7-4): Late limit 7:15 AM.
  - Schedule B (8-5): Late limit 8:15 AM.
  - Schedule C (9-6): Late limit 9:15 AM.
- **Work From Home (WFH)**: Fixed 8-5 schedule. Missing time logs/accomplishments defaults to Absent status.

### 2. Data Processing Pipeline (Jobs & Scheduler)
- **Real-time syncing**: A scheduled Laravel command (`app/Console/Kernel.php`) pulls raw biometric data from `Checkinout` and updates `CheckinoutExt`.
- **Batch Computation**: A queued Job (`App\Jobs\UpdateAttendanceSummary`) uses `AttendanceService` to analyze new check-ins, apply the rules, and persist the calculated status to the `attendance_summaries` table. This strategy guarantees dashboards load instantly.

## Implementation Components (Inertia Vue Stack)

### 1. Backend Layer (Laravel)
- Form requests for clean validation logic.
- Model Scopes and relationships for flexible Database querying.
- `AttendanceService` for enforcing rules.
- Queue Jobs and Schedulers.

### 2. Controller & Routing (Wayfinder)
- **No separate REST APIs are required.** Standard Laravel controllers will resolve data via the `AttendanceService` and pass it to the UI using `Inertia::render()`.
- Routing references in the Vue frontend will use **Wayfinder** (`@laravel/vite-plugin-wayfinder`). We will not hardcode URLs in JS, rather utilizing generated strongly typed functions to request endpoints.

### 3. Frontend Layer (Vue 3 + Tailwind CSS)
- Leverage **Inertia v3** features (Deferred props for loading skeleton states, optimistic form updates, customized exception handling).
- Reusable, dynamic Tailwind CSS visual components for managing Work Arrangements.

## Implementation Phases

### Phase 1: Foundation Setup (Database & Models)
- Generate Migrations for `_ext` tables and `attendance_summaries`.
- Define Eloquent Models, Relationships, and scoped queries using `$table` property modifiers.
- Create Database Seeders (`AttendanceRuleSeeder`, `EmploymentTypeSeeder`).

### Phase 2: Domain Logic & Processing
- Scaffold and build `AttendanceService` housing the core business rules.
- Setup background Jobs, observers, and Artisan commands to synchronize and compute biometric events to summaries.

### Phase 3: Pest Setup & TDD
- Ensure comprehensive Pest PHP feature-tests are written.
- Simulate and validate edge cases associated with fixed flexitime tardiness, WFH absences, and Monday flag ceremony cutoff times through tests.

### Phase 4: UI Development & Controllers
- Create controllers that pass summarized models via `Inertia::render()`.
- Create the Work Arrangement management UI forms.
- Build the performance-optimized Attendance Monitoring Dashboard.
- Setup notifications components via Echo/Reverb if real-time web socket alerting is desired.

## Success Metrics
- Fast, snappier queries leveraging computed batch processing in `attendance_summaries` (< 5s loading).
- Clear separation of backend API bounds vs frontend views handled elegantly by Inertia framework logic.
- Full Pest testing coverage for crucial business rule validations securing against future bugs.