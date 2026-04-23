<?php

use App\Models\Checkinout;
use App\Models\Userinfo;
use App\Models\WorkArrangementExt;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    Event::fake();
    $this->service = new AttendanceService();
    $this->user = Userinfo::forceCreate([
        'Userid' => '1001',
        'Name' => 'Test Employee',
    ]);
});

it('calculates full flexi arrangement incorrectly punched as absent', function () {
    $date = Carbon::parse('2026-04-22');
    
    $summary = $this->service->calculateDailySummary($this->user, $date);

    expect($summary['status'])->toBe('Absent')
        ->and($summary['is_late'])->toBeFalse();
});

it('calculates full flexi arrangement correctly identifying late and undertime', function () {
    WorkArrangementExt::forceCreate([
        'userid' => '1001',
        'arrangement_type' => 'Full Flexi',
        'status' => 'Approved',
    ]);

    $date = Carbon::parse('2026-04-22');
    
    Checkinout::forceCreate(['Userid' => '1001', 'CheckTime' => $date->copy()->setTime(9, 16)]);
    Checkinout::forceCreate(['Userid' => '1001', 'CheckTime' => $date->copy()->setTime(17, 0)]);

    $summary = $this->service->calculateDailySummary($this->user, $date);

    expect($summary['is_late'])->toBeTrue()
        ->and($summary['late_minutes'])->toBe(16)
        ->and($summary['is_undertime'])->toBeTrue()
        ->and($summary['status'])->toBe('Late/Undertime');
});

it('applies monday flag ceremony cutoff appropriately', function () {
    WorkArrangementExt::forceCreate([
        'userid' => '1001',
        'arrangement_type' => 'Full Flexi',
        'status' => 'Approved',
    ]);

    $date = Carbon::parse('2026-04-20'); // Monday
    
    Checkinout::forceCreate(['Userid' => '1001', 'CheckTime' => $date->copy()->setTime(8, 1)]);
    Checkinout::forceCreate(['Userid' => '1001', 'CheckTime' => $date->copy()->setTime(18, 0)]);

    $summary = $this->service->calculateDailySummary($this->user, $date);

    expect($summary['is_late'])->toBeTrue()
        ->and($summary['late_minutes'])->toBe(16)
        ->and($summary['is_undertime'])->toBeFalse();
});

it('verifies fixed flexi schedules', function ($schClassId, $timeIn, $expectedLate, $lateMins) {
    WorkArrangementExt::forceCreate([
        'userid' => '1001',
        'arrangement_type' => 'Fixed Flexi',
        'schclassid' => $schClassId,
        'status' => 'Approved',
    ]);

    $date = Carbon::parse('2026-04-22');
    
    Checkinout::forceCreate(['Userid' => '1001', 'CheckTime' => $date->copy()->setTimeFromTimeString($timeIn)]);
    Checkinout::forceCreate(['Userid' => '1001', 'CheckTime' => $date->copy()->setTime(18, 0)]);

    $summary = $this->service->calculateDailySummary($this->user, $date);

    expect($summary['is_late'])->toBe($expectedLate)
        ->and($summary['late_minutes'])->toBe($lateMins);
})->with([
    'Schedule A (7-4) Late at 7:16' => [1, '07:16:00', true, 16],
    'Schedule A (7-4) On time at 7:15' => [1, '07:15:00', false, 0],
    'Schedule B (8-5) On time at 8:15' => [2, '08:15:00', false, 0],
    'Schedule C (9-6) Late at 9:30' => [3, '09:30:00', true, 30],
]);
