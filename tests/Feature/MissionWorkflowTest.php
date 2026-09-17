<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Mission;
use App\Models\Offre;
use App\Models\User;
use App\Notifications\NouvelleMissionNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_a_categorized_mission(): void
    {
        $client = User::factory()->client()->create();
        $categorie = Categorie::create([
            'nom' => 'Plomberie',
            'description' => 'Travaux de plomberie',
        ]);

        $response = $this->actingAs($client)->post(route('missions.store'), [
            'titre' => 'Réparer une fuite',
            'description' => 'Réparer la fuite sous l’évier.',
            'categorie_id' => $categorie->id,
            'budget' => 350,
            'adresse' => 'Khouribga',
            'date_souhaitee' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect(route('missions.index'));
        $this->assertDatabaseHas('missions', [
            'client_id' => $client->id,
            'categorie_id' => $categorie->id,
            'titre' => 'Réparer une fuite',
        ]);
    }

    public function test_duplicate_mission_submission_creates_only_one_mission(): void
    {
        Notification::fake();

        $client = User::factory()->client()->create();
        User::factory()->prestataire()->create();
        $categorie = Categorie::create([
            'nom' => 'Plomberie',
            'description' => 'Travaux de plomberie',
        ]);

        $payload = [
            'titre' => 'Réparer une fuite',
            'description' => 'Réparer la fuite sous l’évier.',
            'categorie_id' => $categorie->id,
            'budget' => 350,
            'adresse' => 'Khouribga',
            'date_souhaitee' => now()->addDays(3)->toDateString(),
        ];

        $this->actingAs($client)->post(route('missions.store'), $payload)
            ->assertRedirect(route('missions.index'));

        $this->actingAs($client)->post(route('missions.store'), $payload)
            ->assertRedirect(route('missions.index'));

        $this->assertDatabaseCount('missions', 1);
        Notification::assertSentTimes(NouvelleMissionNotification::class, 1);
    }

    public function test_client_can_create_two_distinct_missions_with_the_same_title(): void
    {
        $client = User::factory()->client()->create();
        $categorie = Categorie::create([
            'nom' => 'Menuiserie',
            'description' => 'Travaux de menuiserie',
        ]);

        $firstMission = [
            'titre' => 'Petit meuble',
            'description' => 'Meuble en chêne.',
            'categorie_id' => $categorie->id,
            'budget' => 300,
        ];

        $secondMission = [
            'titre' => 'Petit meuble',
            'description' => 'Meuble en sapin.',
            'categorie_id' => $categorie->id,
            'budget' => 200,
        ];

        $this->actingAs($client)->post(route('missions.store'), $firstMission)
            ->assertRedirect(route('missions.index'));

        $this->actingAs($client)->post(route('missions.store'), $secondMission)
            ->assertRedirect(route('missions.index'));

        $this->assertDatabaseCount('missions', 2);
    }

    public function test_client_can_update_a_mission(): void
    {
        $client = User::factory()->client()->create();
        $categorie = Categorie::create(['nom' => 'Jardinage']);
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'categorie_id' => $categorie->id,
            'titre' => 'Ancien titre',
            'statut' => 'ouverte',
        ]);

        $this->actingAs($client)->put(route('missions.update', $mission), [
            'titre' => 'Nouveau titre pour la mission',
            'description' => 'Description détaillée mise à jour.',
            'categorie_id' => $categorie->id,
            'budget' => 450,
            'adresse' => 'Rabat',
            'date_souhaitee' => now()->addDays(5)->toDateString(),
        ])->assertRedirect(route('missions.index'));

        $this->assertDatabaseHas('missions', [
            'id' => $mission->id,
            'titre' => 'Nouveau titre pour la mission',
            'budget' => 450,
            'adresse' => 'Rabat',
        ]);
    }

    public function test_accepting_an_offer_creates_a_prestation_and_completion_closes_it(): void
    {
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'ouverte',
        ]);
        $offre = Offre::factory()->create([
            'mission_id' => $mission->id,
            'prestataire_id' => $prestataire->id,
            'statut' => 'en_attente',
            'delai_execution' => 5,
        ]);

        $response = $this->actingAs($client)->patch(route('offres.accept', $offre));

        $response->assertRedirect();
        $this->assertDatabaseHas('prestations', [
            'offre_id' => $offre->id,
            'mission_id' => $mission->id,
            'prestataire_id' => $prestataire->id,
            'statut' => 'en_cours',
        ]);

        $this->actingAs($client)->patch(route('missions.complete', $mission));

        $this->assertDatabaseHas('prestations', [
            'offre_id' => $offre->id,
            'statut' => 'terminee',
        ]);
    }

    public function test_an_offer_cannot_be_accepted_twice(): void
    {
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'ouverte',
        ]);
        $offre = Offre::factory()->create([
            'mission_id' => $mission->id,
            'prestataire_id' => $prestataire->id,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($client)->patch(route('offres.accept', $offre));

        $this->actingAs($client)
            ->patch(route('offres.accept', $offre))
            ->assertStatus(409);

        $this->assertDatabaseCount('prestations', 1);
    }

    public function test_prestataire_can_update_and_cancel_a_pending_offer(): void
    {
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'ouverte',
        ]);
        $offre = Offre::factory()->create([
            'mission_id' => $mission->id,
            'prestataire_id' => $prestataire->id,
            'statut' => 'en_attente',
        ]);

        $this->actingAs($prestataire)
            ->put(route('offres.update', $offre), [
                'prix_propose' => 700,
                'message' => 'Nouvelle proposition',
                'delai_execution' => 7,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offres', [
            'id' => $offre->id,
            'prix_propose' => 700,
            'delai_execution' => 7,
        ]);

        $this->actingAs($prestataire)
            ->delete(route('offres.cancel', $offre))
            ->assertRedirect();

        $this->assertDatabaseHas('offres', [
            'id' => $offre->id,
            'statut' => 'refusee',
        ]);
    }

    public function test_prestataire_can_filter_open_missions_by_category(): void
    {
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $categorie = Categorie::create(['nom' => 'Électricité']);
        $matchingMission = Mission::factory()->create([
            'client_id' => $client->id,
            'categorie_id' => $categorie->id,
            'titre' => 'Installation électrique',
            'statut' => 'ouverte',
        ]);
        Mission::factory()->create([
            'client_id' => $client->id,
            'titre' => 'Travaux de peinture',
            'statut' => 'ouverte',
        ]);

        $this->actingAs($prestataire)
            ->get(route('prestataire.missions.index', [
                'categorie_id' => $categorie->id,
            ]))
            ->assertOk()
            ->assertSee($matchingMission->titre)
            ->assertDontSee('Travaux de peinture');
    }
}
