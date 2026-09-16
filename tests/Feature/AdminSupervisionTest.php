<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\Mission;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSupervisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_business_entities(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.missions.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.offres.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.prestations.index'))->assertOk();
    }

    public function test_non_admin_cannot_view_business_entities(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->get(route('admin.missions.index'))->assertForbidden();
        $this->actingAs($client)->get(route('admin.offres.index'))->assertForbidden();
        $this->actingAs($client)->get(route('admin.prestations.index'))->assertForbidden();
    }

    public function test_admin_can_moderate_missions_offers_and_evaluations(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'ouverte',
        ]);
        $missionOffre = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'ouverte',
        ]);
        $offre = Offre::factory()->create([
            'mission_id' => $missionOffre->id,
            'prestataire_id' => $prestataire->id,
            'statut' => 'en_attente',
        ]);
        $evaluation = Evaluation::create([
            'mission_id' => $mission->id,
            'client_id' => $client->id,
            'prestataire_id' => $prestataire->id,
            'note' => 4,
            'commentaire' => 'Bon travail',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.missions.cancel', $mission))
            ->assertRedirect();
        $this->assertDatabaseHas('missions', [
            'id' => $mission->id,
            'statut' => 'annulee',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.offres.refuse', $offre))
            ->assertRedirect();
        $this->assertDatabaseHas('offres', [
            'id' => $offre->id,
            'statut' => 'refusee',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.evaluations.destroy', $evaluation))
            ->assertRedirect();
        $this->assertDatabaseMissing('evaluations', ['id' => $evaluation->id]);
    }
}
