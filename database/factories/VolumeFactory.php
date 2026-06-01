<?php

namespace Database\Factories;

use App\Models\Volume;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolumeFactory extends Factory
{
    protected $model = Volume;

    public function definition(): array
    {
        return [
            'journal_id' => Journal::factory(),
            'number' => $this->faker->numberBetween(1, 50),
            'year' => $this->faker->year,
        ];
    }
}
