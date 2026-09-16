<?php

namespace Database\Factories;

use App\Models\Mission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mission>
 */
class MissionFactory extends Factory
{
    protected $model = Mission::class;

    public function definition(): array
    {
        return [
            'client_id' => null,
            'categorie_id' => null,

            'titre' => fake()->randomElement([
                'Réparation d’une fuite d’eau',
                'Installation d’un luminaire',
                'Peinture d’un appartement',
                'Montage d’une armoire',
                'Réparation d’une porte',
                'Entretien d’un jardin',
                'Installation d’un climatiseur',
                'Petits travaux de maçonnerie',
            ]),

            'description' => fake()->randomElement([
                'Je cherche un prestataire sérieux pour réaliser cette intervention à domicile.',
                'Besoin d’une personne expérimentée pour effectuer les travaux proprement et rapidement.',
                'Travaux à réaliser avec soin. Le matériel principal sera disponible sur place.',
                'Je souhaite obtenir une intervention professionnelle avec un travail propre et soigné.',
            ]),

            'budget' => fake()->randomFloat(2, 150, 2500),

            'adresse' => fake()->randomElement([
                'Hay Al Qods, Khouribga',
                'Centre-ville, Khouribga',
                'Hay Al Amal, Khouribga',
                'Hay Al Wahda, Khouribga',
                'Hay Al Massira, Khouribga',
            ]),

            'date_souhaitee' => fake()->optional()->dateTimeBetween('today', '+30 days'),

            'statut' => fake()->randomElement([
                'ouverte',
                'ouverte',
                'ouverte',
                'en_cours',
                'terminee',
            ]),
        ];
    }
}
