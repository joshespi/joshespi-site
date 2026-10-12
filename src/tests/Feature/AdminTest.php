<?php

namespace Tests\Feature;

use App\Enums\IntakeStatus;
use App\Livewire\Admin\IntakeIndex;
use App\Livewire\Admin\IntakeShow;
use App\Mail\IntakeConfirmation;
use App\Models\Intake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $intake = Intake::factory()->create();

        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get(route('admin.intakes.show', $intake))->assertRedirect(route('admin.login'));
    }

    public function test_login_page_renders_and_is_kept_out_of_search(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', escape: false);
    }

    public function test_correct_credentials_sign_in(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);

        $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'correct-horse-battery'])
            ->assertRedirect(route('admin.intakes.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);

        $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'nope'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_locks_out_after_five_failures(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'nope']);
        }

        // Even the right password is refused until the window passes.
        $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'correct-horse-battery'])
            ->assertSessionHasErrors('email');

        // The seconds count down while the suite runs, so match the stem only.
        $this->assertStringStartsWith('Too many attempts.', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_index_lists_intakes(): void
    {
        Intake::factory()->create(['name' => 'Jane Client']);

        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Jane Client');
    }

    public function test_index_filters_by_status_and_failed_email(): void
    {
        Intake::factory()->create(['name' => 'Quoted Person', 'status' => IntakeStatus::Quoted]);
        Intake::factory()->undelivered()->create(['name' => 'Bounced Person']);

        Livewire::test(IntakeIndex::class)
            ->set('filter', 'quoted')
            ->assertSee('Quoted Person')
            ->assertDontSee('Bounced Person')
            ->set('filter', IntakeIndex::UNDELIVERED)
            ->assertSee('Bounced Person')
            ->assertDontSee('Quoted Person');
    }

    public function test_failed_email_flag_follows_the_same_rule_as_the_filter(): void
    {
        // Spam is left out of "Email failed" everywhere, not just the filter.
        Intake::factory()->undelivered()->create(['status' => IntakeStatus::Spam]);

        Livewire::test(IntakeIndex::class)
            ->assertDontSee('email failed')
            ->assertDontSee('Email failed');
    }

    public function test_show_page_has_the_full_message(): void
    {
        $intake = Intake::factory()->create([
            'message' => "Line one of a long message.\nLine two.",
            'details' => ['Hosting provider' => 'SiteGround'],
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.intakes.show', $intake))
            ->assertOk()
            ->assertSee('Line one of a long message.')
            ->assertSee('Line two.')
            ->assertSee('SiteGround');
    }

    public function test_a_javascript_url_is_never_rendered_as_a_link(): void
    {
        $intake = Intake::factory()->create(['url' => 'javascript:alert(1)']);

        Livewire::test(IntakeShow::class, ['intake' => $intake])
            ->assertDontSeeHtml('href="javascript:')
            ->assertSee('javascript:alert(1)');
    }

    public function test_notes_can_be_added_and_deleted(): void
    {
        $intake = Intake::factory()->create();

        $component = Livewire::test(IntakeShow::class, ['intake' => $intake])
            ->set('note', 'Sent the scope doc on Tuesday.')
            ->call('addNote')
            ->assertHasNoErrors()
            ->assertSet('note', '')
            ->assertSee('Sent the scope doc on Tuesday.');

        $note = $intake->notes()->sole();

        $component->call('deleteNote', $note->id)
            ->assertDontSee('Sent the scope doc');

        $this->assertDatabaseCount('intake_notes', 0);
    }

    public function test_a_note_cannot_delete_another_intakes_note(): void
    {
        $mine = Intake::factory()->create();
        $other = Intake::factory()->create();
        $otherNote = $other->notes()->create(['body' => 'Not yours']);

        Livewire::test(IntakeShow::class, ['intake' => $mine])
            ->call('deleteNote', $otherNote->id);

        $this->assertModelExists($otherNote);
    }

    public function test_empty_note_is_rejected(): void
    {
        Livewire::test(IntakeShow::class, ['intake' => Intake::factory()->create()])
            ->call('addNote')
            ->assertHasErrors(['note' => 'required']);
    }

    public function test_status_can_be_changed(): void
    {
        $intake = Intake::factory()->create();

        Livewire::test(IntakeShow::class, ['intake' => $intake])
            ->set('status', 'quoted')
            ->assertHasNoErrors();

        $this->assertSame(IntakeStatus::Quoted, $intake->fresh()->status);
    }

    public function test_unknown_status_is_rejected(): void
    {
        $intake = Intake::factory()->create();

        Livewire::test(IntakeShow::class, ['intake' => $intake])
            ->set('status', 'bogus')
            ->assertHasErrors('status');

        $this->assertSame(IntakeStatus::New, $intake->fresh()->status);
    }

    public function test_a_failed_email_can_be_resent_from_the_page(): void
    {
        Mail::fake();
        $intake = Intake::factory()->undelivered()->create();

        Livewire::test(IntakeShow::class, ['intake' => $intake])
            ->call('resend', 'confirmation');

        Mail::assertSent(IntakeConfirmation::class, 1);
        $this->assertNotNull($intake->fresh()->confirmation_sent_at);
        $this->assertNull($intake->fresh()->confirmation_error);
        $this->assertNull($intake->fresh()->notification_sent_at);
    }

    public function test_intake_can_be_deleted_with_its_notes(): void
    {
        $intake = Intake::factory()->create();
        $intake->notes()->create(['body' => 'A note']);

        Livewire::test(IntakeShow::class, ['intake' => $intake])
            ->call('delete')
            ->assertRedirect(route('admin.intakes.index'));

        $this->assertModelMissing($intake);
        $this->assertDatabaseCount('intake_notes', 0);
    }
}
