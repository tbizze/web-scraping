<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pessoa>
 */
class PessoaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'data_nascimento' => $this->faker->dateTimeBetween('-30 years', 'now'),
            'sexo' => $this->faker->randomElement(['F', 'M']),
            'telefone' => $this->faker->phoneNumber(),
            'pessoa_status_id' => $this->faker->randomElement([1, 2, 3, 4]),
        ];
    }
}
