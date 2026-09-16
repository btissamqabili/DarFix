<?php

namespace Database\Factories;

use App\Models\Evaluation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        return [
            'mission_id' => null,

            'client_id' => null,

            'prestataire_id' => null,

            'note' => fake()->numberBetween(3, 5),

            'commentaire' => fake()->randomElement([
                'Très bon travail, prestataire sérieux et ponctuel.',
                'Intervention rapide et travail de qualité.',
                'Très satisfait du résultat. Je recommande ce prestataire.',
                'Travail professionnel et bonne communication.',
                'Prestation correcte et réalisée dans les délais.',
            ]),
        ];
    }
}
