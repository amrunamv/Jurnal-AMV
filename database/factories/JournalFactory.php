<?php

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JournalFactory extends Factory
{
    protected $model = Journal::class;

    public function definition(): array
    {
        $name = $this->faker->sentence(3);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph,
            'issn' => $this->faker->numerify('####-####'),
            'e_issn' => $this->faker->numerify('####-####'),
            'is_active' => true,
        ];
    }
}
