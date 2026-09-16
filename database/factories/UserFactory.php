<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),

            'email' => fake()->unique()->safeEmail(),

            'email_verified_at' => now(),

            'password' => static::$password ??= Hash::make('password'),

            'remember_token' => Str::random(10),

            'role' => 'client',

            'description' => null,

            'competences' => null,

            'experience' => null,

            'disponibilite' => null,

            'telephone' => fake()->phoneNumber(),

            'adresse' => fake()->address(),

            'photo' => null,
        ];
    }

    /**
     * Create a client.
     */
    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'client',
            'description' => null,
            'competences' => null,
            'experience' => null,
            'disponibilite' => null,
        ]);
    }

    /**
     * Create a prestataire.
     */
    public function prestataire(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'prestataire',

            'description' => fake()->paragraph(),

            'competences' => fake()->randomElement([
                'Plomberie',
                'Électricité',
                'Peinture',
                'Menuiserie',
                'Climatisation',
                'Jardinage',
                'Maçonnerie',
            ]),

            'experience' => fake()->numberBetween(1, 15),

            'disponibilite' => fake()->randomElement([
                'Disponible immédiatement',
                'En semaine',
                'Le week-end',
                'Selon disponibilité',
            ]),
        ]);
    }

    /**
     * Create an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'description' => null,
            'competences' => null,
            'experience' => null,
            'disponibilite' => null,
        ]);
    }

    /**
     * Indicate that the user's email should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
