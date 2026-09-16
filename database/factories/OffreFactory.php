<?php

namespace Database\Factories;

use App\Models\Offre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offre>
 */
class OffreFactory extends Factory
{
    protected $model = Offre::class;

    public function definition(): array
    {
        return [
            'mission_id' => null,

            'prestataire_id' => null,

            'prix_propose' => fake()->randomFloat(
                2,
                150,
                2000
            ),

            'message' => fake()->randomElement([
                'Bonjour, je suis disponible pour réaliser cette mission avec sérieux et professionnalisme.',
                'Je peux intervenir rapidement et effectuer le travail proprement.',
                'Je possède une bonne expérience dans ce type de travaux et je reste disponible pour intervenir.',
                'Je serais ravi de réaliser cette mission. Je peux commencer selon vos disponibilités.',
            ]),

            'delai_execution' => fake()->numberBetween(1, 30),

            'statut' => fake()->randomElement([
                'en_attente',
                'en_attente',
                'acceptee',
                'refusee',
            ]),
        ];
    }
}
