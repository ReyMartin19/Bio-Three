<?php

namespace Database\Factories;

use App\Models\Checkinout;
use App\Models\Userinfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkinout>
 */
class CheckinoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'Userid' => Userinfo::factory(),
            'CheckTime' => fake()->dateTimeBetween('-1 week', 'now'),
            'CheckType' => fake()->randomElement(['I', 'O']), // In or Out
            'Sensorid' => 1,
            'Checked' => 0,
            'Exported' => 0,
            'OpenDoorFlag' => 0,
        ];
    }
}
