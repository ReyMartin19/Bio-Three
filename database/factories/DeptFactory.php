<?php

namespace Database\Factories;

use App\Models\Dept;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dept>
 */
class DeptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'DeptName' => fake()->company(),
            'SupDeptid' => 1,
        ];
    }
}
