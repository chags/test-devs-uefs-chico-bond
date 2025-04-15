<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * O model associado a esta factory.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define o estado padrão do model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;

        return [
            'title'    => $title,
            'content'  => $this->faker->paragraph,
            'slug'     => Str::slug($title),
            // Garante que um usuário válido seja criado e seu ID seja atribuído
            // Outra abordagem na factory de Post:
            'user_id' => fn() => User::factory()->create()->id,
 
        ];
    }
}

// Exemplo da factory de Post:
