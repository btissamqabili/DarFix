<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Evaluation;
use App\Models\Mission;
use App\Models\Offre;
use App\Models\Prestation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Utilisateurs principaux
        |--------------------------------------------------------------------------
        */

        $admin = User::firstOrCreate(
            ['email' => 'admin@bricolink.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $clientPrincipal = User::firstOrCreate(
            ['email' => 'client@bricolink.com'],
            [
                'name' => 'Client Test',
                'password' => Hash::make('password123'),
                'role' => 'client',
            ]
        );

        $prestatairePrincipal = User::firstOrCreate(
            ['email' => 'prestataire@bricolink.com'],
            [
                'name' => 'Prestataire Test',
                'password' => Hash::make('password123'),
                'role' => 'prestataire',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Clients supplémentaires
        |--------------------------------------------------------------------------
        */

        $clients = collect([$clientPrincipal]);

        for ($i = 1; $i <= 4; $i++) {
            $clients->push(
                User::firstOrCreate(
                    ['email' => "client{$i}@bricolink.com"],
                    [
                        'name' => "Client {$i}",
                        'password' => Hash::make('password123'),
                        'role' => 'client',
                    ]
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prestataires supplémentaires
        |--------------------------------------------------------------------------
        */

        $prestataires = collect([$prestatairePrincipal]);

        for ($i = 1; $i <= 4; $i++) {
            $prestataires->push(
                User::firstOrCreate(
                    ['email' => "prestataire{$i}@bricolink.com"],
                    [
                        'name' => "Prestataire {$i}",
                        'password' => Hash::make('password123'),
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
                    ]
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Catégories
        |--------------------------------------------------------------------------
        */

        $categoriesData = [
            'Plomberie' => 'Réparation et installation de plomberie à domicile.',
            'Électricité' => 'Installation, réparation et maintenance électrique.',
            'Peinture' => 'Travaux de peinture intérieure et extérieure.',
            'Menuiserie' => 'Fabrication et réparation des éléments en bois.',
            'Maçonnerie' => 'Petits travaux de maçonnerie et rénovation.',
            'Jardinage' => 'Entretien et aménagement des espaces verts.',
            'Climatisation' => 'Installation et entretien des climatiseurs.',
            'Montage de meubles' => 'Montage et installation de meubles à domicile.',
        ];

        $categories = collect();

        foreach ($categoriesData as $nom => $description) {
            $categories->push(
                Categorie::firstOrCreate(
                    ['nom' => $nom],
                    ['description' => $description]
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        $servicesData = [
            'Réparation de fuite d’eau',
            'Installation électrique',
            'Peinture intérieure',
            'Montage de meubles',
            'Réparation de porte',
            'Entretien de jardin',
            'Installation de climatiseur',
            'Petits travaux de maçonnerie',
        ];

        foreach ($prestataires as $index => $prestataire) {

            $categorie = $categories[$index % $categories->count()];

            $nomService = $servicesData[$index % count($servicesData)];

            Service::firstOrCreate(
                [
                    'prestataire_id' => $prestataire->id,
                    'nom' => $nomService,
                ],
                [
                    'categorie_id' => $categorie->id,
                    'description' => fake()->sentence(15),
                    'prix' => fake()->randomFloat(2, 150, 1200),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Missions
        |--------------------------------------------------------------------------
        */

        $missions = collect();

        $missionsData = [
            [
                'titre' => 'Réparation d’une fuite d’eau',
                'description' => 'Réparer une fuite sous l’évier de la cuisine et vérifier les raccordements.',
                'budget' => 350,
                'adresse' => 'Hay Al Qods, Khouribga',
                'statut' => 'terminee',
            ],
            [
                'titre' => 'Installation d’un luminaire',
                'description' => 'Installer un nouveau luminaire dans le salon avec vérification du câblage.',
                'budget' => 250,
                'adresse' => 'Hay Al Amal, Khouribga',
                'statut' => 'en_cours',
            ],
            [
                'titre' => 'Peinture d’une chambre',
                'description' => 'Repeindre une chambre avec préparation des murs et finition propre.',
                'budget' => 900,
                'adresse' => 'Hay Al Wahda, Khouribga',
                'statut' => 'ouverte',
            ],
            [
                'titre' => 'Montage d’une armoire',
                'description' => 'Assembler et installer une armoire dans une chambre.',
                'budget' => 300,
                'adresse' => 'Centre-ville, Khouribga',
                'statut' => 'ouverte',
            ],
            [
                'titre' => 'Entretien du jardin',
                'description' => 'Nettoyage, taille des plantes et entretien général du jardin.',
                'budget' => 450,
                'adresse' => 'Hay Al Massira, Khouribga',
                'statut' => 'terminee',
            ],
            [
                'titre' => 'Réparation d’une porte',
                'description' => 'Réparer une porte intérieure qui ferme difficilement et vérifier les charnières.',
                'budget' => 400,
                'adresse' => 'Hay Al Qods, Khouribga',
                'statut' => 'ouverte',
            ],
        ];

        foreach ($clients as $clientIndex => $client) {

            foreach ($missionsData as $missionIndex => $data) {

                if ($missionIndex >= 2 && $clientIndex > 1) {
                    break;
                }

                $mission = Mission::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'titre' => $data['titre'],
                    ],
                    [
                        'description' => $data['description'],
                        'budget' => $data['budget'],
                        'adresse' => $data['adresse'],
                        'categorie_id' => $categories[$missionIndex % $categories->count()]->id,
                        'date_souhaitee' => now()->addDays($missionIndex + 1)->toDateString(),
                        'statut' => $data['statut'],
                    ]
                );

                $missions->push($mission);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Offres
        |--------------------------------------------------------------------------
        */

        foreach ($missions as $mission) {

            /*
             * On ajoute quelques offres pour les missions ouvertes/en cours.
             */
            $prestatairesPourMission = $prestataires
                ->shuffle()
                ->take(2);

            foreach ($prestatairesPourMission as $index => $prestataire) {

                Offre::firstOrCreate(
                    [
                        'mission_id' => $mission->id,
                        'prestataire_id' => $prestataire->id,
                    ],
                    [
                        'prix_propose' => max(
                            100,
                            ($mission->budget ?? 500) - ($index * 50)
                        ),
                        'message' => fake()->randomElement([
                            'Bonjour, je suis disponible pour réaliser cette mission.',
                            'Je peux intervenir rapidement et effectuer le travail proprement.',
                            'Je possède une bonne expérience dans ce type de travaux.',
                            'Je serais ravi de réaliser cette mission.',
                        ]),
                        'delai_execution' => fake()->numberBetween(1, 14),
                        'statut' => 'en_attente',
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Finalisation de certaines missions
        |--------------------------------------------------------------------------
        */

        $missionsTerminees = $missions
            ->filter(fn (Mission $mission) => $mission->statut === 'terminee')
            ->values();

        foreach ($missionsTerminees as $mission) {

            $offre = $mission->offres()->first();

            if (! $offre) {
                continue;
            }

            $offre->update([
                'statut' => 'acceptee',
            ]);

            Prestation::updateOrCreate(
                ['offre_id' => $offre->id],
                [
                    'mission_id' => $mission->id,
                    'prestataire_id' => $offre->prestataire_id,
                    'date_debut' => now()->subDays(3),
                    'date_fin' => now(),
                    'statut' => 'terminee',
                    'montant' => $offre->prix_propose,
                ]
            );

            Offre::where('mission_id', $mission->id)
                ->where('id', '!=', $offre->id)
                ->update([
                    'statut' => 'refusee',
                ]);

            Evaluation::firstOrCreate(
                [
                    'mission_id' => $mission->id,
                    'client_id' => $mission->client_id,
                ],
                [
                    'prestataire_id' => $offre->prestataire_id,
                    'note' => fake()->numberBetween(3, 5),
                    'commentaire' => fake()->randomElement([
                        'Très bon travail, prestataire sérieux et ponctuel.',
                        'Intervention rapide et travail de qualité.',
                        'Très satisfait du résultat.',
                        'Travail professionnel et bonne communication.',
                    ]),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Messages de fin
        |--------------------------------------------------------------------------
        */

        $this->command?->info('Seed terminé avec succès.');
        $this->command?->info("Clients : {$clients->count()}");
        $this->command?->info("Prestataires : {$prestataires->count()}");
        $this->command?->info("Catégories : {$categories->count()}");
        $this->command?->info("Missions : {$missions->count()}");
    }
}
