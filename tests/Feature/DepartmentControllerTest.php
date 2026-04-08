<?php

use App\Models\Dept;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests cannot access department index', function () {
    $this->get(route('departments.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can access department index', function () {
    Dept::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->get(route('departments.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('departments/Index')
            ->has('departments', 3)
        );
});

test('departments can be created', function () {
    $this->actingAs($this->user)
        ->post(route('departments.store'), [
            'DeptName' => 'IT Department',
            'SupDeptid' => 1,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('dept', [
        'DeptName' => 'IT Department',
        'SupDeptid' => 1,
    ]);
});

test('departments can be updated', function () {
    $dept = Dept::factory()->create(['DeptName' => 'Old Name']);

    $this->actingAs($this->user)
        ->put(route('departments.update', $dept->Deptid), [
            'DeptName' => 'New Name',
            'SupDeptid' => 0,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('dept', [
        'Deptid' => $dept->Deptid,
        'DeptName' => 'New Name',
    ]);
});

test('departments can be deleted', function () {
    $dept = Dept::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('departments.destroy', $dept->Deptid))
        ->assertRedirect();

    $this->assertDatabaseMissing('dept', [
        'Deptid' => $dept->Deptid,
    ]);
});
