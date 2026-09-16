<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'prestataire_id' => User::factory()->prestataire(),

            'categorie_id' => Categorie::factory(),

            'nom' => fake()->randomElement([
                'Réparation plomberie',
                'Installation électrique',
                'Travaux de peinture',
                'Montage de meubles',
                'Réparation de portes',
                'Entretien de jardin',
                'Installation climatisation',
                'Petite maçonnerie',
            ]),

            'description' => fake()->paragraph(),

            'prix' => fake()->randomFloat(
                2,
                100,
                1500
            ),
        ];
    }
}
