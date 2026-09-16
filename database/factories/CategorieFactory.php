<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categorie>
 */
class CategorieFactory extends Factory
{
    protected $model = Categorie::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->randomElement([
                'Plomberie',
                'Électricité',
                'Peinture',
                'Menuiserie',
                'Maçonnerie',
                'Jardinage',
                'Climatisation',
                'Montage de meubles',
            ]),

            'description' => fake()->sentence(12),
        ];
    }
}
