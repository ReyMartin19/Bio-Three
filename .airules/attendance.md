# Attendance Calculation Rules (AI summary)

## Where To Put It

- **Model:** Per-record helpers (accessors, mutators, scopes) belong on the model — e.g., use `app/Models/Checkinout.php` for methods like `isLate()` or `scopeForDay()`. Keep these methods small and focused.
- **Eloquent Scopes:** Query filters (e.g., `scopeLate`, `scopeAbsent`) are good for reuse in queries and reports.
- **Service / Domain Class (recommended):** Complex business rules (tardiness windows, absence rules, aggregations across models) belong in a service, e.g. `app/Services/AttendanceService.php`. Services are easy to unit-test and keep controllers/models thin.
- **Observers / Events + Jobs:** Side-effects (update summaries, notify managers) should use model observers or events that dispatch queued `Job`s, e.g. `app/Jobs/UpdateAttendanceSummary` for async work.
- **Console Command / Scheduler:** Scheduled recalculations (daily/monthly summaries) belong in `app/Console/Commands` and are wired in `app/Console/Kernel.php` schedule.
- **Repository / Query Objects:** Put complex DB queries in dedicated query classes (e.g., `app/Queries/AttendanceQuery.php`) to keep SQL/DB logic separate from services.
- **Controllers / Form Requests:** Controllers should only orchestrate (call services) and return responses; validation belongs in `FormRequest`s.

## Architecture & Data

- **Realtime vs batch:** Compute on read for ad-hoc reports, or compute and persist to an `attendance_summaries` table for fast dashboards. Use jobs/observers to keep summaries in sync.
- **Caching:** Cache expensive aggregates with clear invalidation (on new checkins/changes).
- **Tests:** Unit-test the service and scopes; add feature tests for scheduled commands or API endpoints (this repo uses Pest).

## Minimal examples

- Model scope (in `Checkinout`):

```php
public function scopeLate($query, $date)
{
    return $query->whereDate('checkin_at', $date)
                 ->where('checkin_at', '>', $this->scheduled_start);
}
```

- Service sketch:

`app/Services/AttendanceService.php` with a method `calculateTardiness(User $user, DateRange $range): TardinessResult` that encapsulates rules and returns structured results.

- Observer flow:

`Checkinout::created` -> dispatch `UpdateAttendanceSummary` (queued job).

## Next steps

- Decide whether to compute-on-read or persist summaries.
- I can scaffold `AttendanceService`, example scopes, a queued job, and tests if you want — say the word and I will draft them.
