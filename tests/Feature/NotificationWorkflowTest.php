<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Conversation;
use App\Models\Evaluation;
use App\Models\Mission;
use App\Models\Offre;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use App\Notifications\NouvelleEvaluationNotification;
use App\Notifications\NouvelleMissionNotification;
use App\Notifications\NouvelleOffreNotification;
use App\Notifications\OffreAcceptedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_mission_notifies_prestataires(): void
    {
        Notification::fake();

        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $categorie = Categorie::create(['nom' => 'Menuiserie']);

        $this->actingAs($client)->post(route('missions.store'), [
            'titre' => 'Fabrication meuble TV',
            'description' => 'Meuble sur mesure en chêne.',
            'categorie_id' => $categorie->id,
            'budget' => 1200,
            'adresse' => 'Casablanca',
            'date_souhaitee' => now()->addDays(5)->toDateString(),
        ])->assertRedirect(route('missions.index'));

        Notification::assertSentTo(
            $prestataire,
            NouvelleMissionNotification::class,
            function ($notification) {
                return $notification->mission->titre === 'Fabrication meuble TV';
            }
        );
    }

    public function test_submitting_an_offer_notifies_the_client(): void
    {
        Notification::fake();

        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'ouverte',
        ]);

        $this->actingAs($prestataire)->post(route('offres.store', $mission), [
            'prix_propose' => 850,
            'delai_execution' => 4,
            'message' => 'Je dispose des outils adaptés.',
        ])->assertRedirect();

        Notification::assertSentTo(
            $client,
            NouvelleOffreNotification::class,
            function ($notification) use ($mission) {
                return $notification->missionId === $mission->id
                    && $notification->prixPropose === 850.0;
            }
        );
    }

    public function test_accepting_an_offer_notifies_the_prestataire(): void
    {
        Notification::fake();

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

        $this->actingAs($client)->patch(route('offres.accept', $offre))->assertRedirect();

        Notification::assertSentTo(
            $prestataire,
            OffreAcceptedNotification::class,
            function ($notification) use ($offre) {
                return $notification->offre->id === $offre->id;
            }
        );
    }

    public function test_sending_a_message_notifies_the_recipient(): void
    {
        Notification::fake();

        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $conversation = Conversation::create([
            'client_id' => $client->id,
            'prestataire_id' => $prestataire->id,
        ]);

        $this->actingAs($client)->post(route('messages.store', $conversation), [
            'contenu' => 'Bonjour, êtes-vous disponible demain ?',
        ])->assertRedirect(route('conversations.show', $conversation));

        Notification::assertSentTo(
            $prestataire,
            NewMessageNotification::class,
            function ($notification) use ($conversation) {
                return $notification->conversationId === $conversation->id
                    && $notification->messageContent === 'Bonjour, êtes-vous disponible demain ?';
            }
        );
    }

    public function test_submitting_an_evaluation_notifies_the_prestataire(): void
    {
        Notification::fake();

        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'terminee',
        ]);
        Offre::factory()->create([
            'mission_id' => $mission->id,
            'prestataire_id' => $prestataire->id,
            'statut' => 'acceptee',
        ]);

        $this->actingAs($client)->post(route('evaluations.store', $mission), [
            'note' => 5,
            'commentaire' => 'Prestation impeccable et soignée.',
        ])->assertRedirect();

        Notification::assertSentTo(
            $prestataire,
            NouvelleEvaluationNotification::class,
            function ($notification) {
                return $notification->evaluation->note === 5;
            }
        );
    }

    public function test_notification_redirection_for_client_on_new_offer(): void
    {
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'ouverte',
        ]);

        $client->notify(new NouvelleOffreNotification(
            $mission->id,
            $prestataire->name,
            500.0
        ));

        $notification = $client->notifications()->first();

        $response = $this->actingAs($client)->get(route('notifications.read', $notification->id));
        $response->assertRedirect(route('missions.offres', $mission));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_notification_redirection_for_prestataire_on_evaluation_points_to_public_profile(): void
    {
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'statut' => 'terminee',
        ]);
        $evaluation = Evaluation::create([
            'mission_id' => $mission->id,
            'client_id' => $client->id,
            'prestataire_id' => $prestataire->id,
            'note' => 5,
            'commentaire' => 'Excellent travail',
        ]);

        $prestataire->notify(new NouvelleEvaluationNotification($evaluation));
        $notification = $prestataire->notifications()->first();

        $response = $this->actingAs($prestataire)->get(route('notifications.read', $notification->id));
        $response->assertRedirect(route('prestataires.show', $prestataire->id));
    }

    public function test_notification_handles_deleted_mission_gracefully_without_404(): void
    {
        $client = User::factory()->client()->create();
        $prestataire = User::factory()->prestataire()->create();

        $client->notify(new NouvelleOffreNotification(
            99999, // Mission inexistant
            $prestataire->name,
            400.0
        ));

        $notification = $client->notifications()->first();

        $response = $this->actingAs($client)->get(route('notifications.read', $notification->id));
        $response->assertRedirect(route('missions.index'));
        $response->assertSessionHas('error');
    }

    public function test_all_authenticated_roles_can_view_prestataire_profile(): void
    {
        $prestataire = User::factory()->prestataire()->create();
        $client = User::factory()->client()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($client)->get(route('prestataires.show', $prestataire->id))->assertOk();
        $this->actingAs($prestataire)->get(route('prestataires.show', $prestataire->id))->assertOk();
        $this->actingAs($admin)->get(route('prestataires.show', $prestataire->id))->assertOk();
    }
}
