<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class UserFactory extends Factory
{
    /**
     * Define o estado padrão da UserFactory.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name, // Gera um nome aleatório
            'email' => $this->faker->unique()->safeEmail, // Gera um email único e válido
            'password' => bcrypt('password'), // Define uma senha padrão criptografada
        ];
    }
}