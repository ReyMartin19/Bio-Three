<?php

namespace Database\Factories;

use App\Models\Dept;
use App\Models\Userinfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Userinfo>
 */
class UserinfoFactory extends Factory
{
    public function definition(): array
    {
        // Custom PK length for Userid (String for BIOTRACE)
        $userId = (string) fake()->unique()->numberBetween(1000, 99999);

        return [
            'Userid' => $userId,
            'UserCode' => $userId,
            'Name' => fake()->name(),
            'Deptid' => Dept::factory(),
            'Sex' => fake()->randomElement(['Male', 'Female']),
            'IsAtt' => 1,
            'CardNum' => fake()->numerify('##########'),
        ];
    }
}
