<?php

namespace Database\Factories;

use App\Models\Dept;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dept>
 */
class DeptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Dept::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'DeptName' => $this->faker->unique()->word(),
            'SupDeptid' => 0,
        ];
    }
}
