<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * O model associado a esta factory.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define o estado padrão do model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word;

        return [
            'name' => $this->faker->unique()->word,
            'slug' => $this->faker->unique()->slug,
        ];
    }
}
