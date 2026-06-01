<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Volume;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        $number = $this->faker->numberBetween(1, 12);
        return [
            'volume_id' => \App\Models\Volume::factory(),
            'number' => $number,
            'slug' => Str::random(10),
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
