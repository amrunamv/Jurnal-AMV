<?php

namespace Database\Factories;

use App\Models\Journal;
use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ManuscriptFactory extends Factory
{
    protected $model = Manuscript::class;

    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'uuid' => (string) Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'abstract' => $this->faker->paragraphs(3, true),
            'keywords' => $this->faker->words(5),
            'status' => Manuscript::STATUS_DRAFT,
            'journal_id' => Journal::factory(),
            'submitter_id' => User::factory(),
            'view_count' => 0,
            'download_count' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Manuscript::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }
}
