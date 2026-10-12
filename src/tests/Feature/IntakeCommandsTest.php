<?php

namespace Tests\Feature;

use App\Mail\IntakeConfirmation;
use App\Mail\IntakeNotification;
use App\Models\Intake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IntakeCommandsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['mail.intake_notify_address' => 'notify@example.com']);
        Mail::fake();
    }

    public function test_resend_retries_only_what_never_went_out(): void
    {
        $delivered = Intake::factory()->create();
        $failed = Intake::factory()->undelivered()->create();

        $this->artisan('intake:resend')
            ->expectsOutputToContain('2 sent, 0 failed.')
            ->assertSuccessful();

        Mail::assertSent(IntakeNotification::class, 1);
        Mail::assertSent(IntakeConfirmation::class, 1);
        $this->assertNotNull($failed->fresh()->notification_sent_at);
        $this->assertNotNull($failed->fresh()->confirmation_sent_at);
        $this->assertEquals($delivered->notification_sent_at, $delivered->fresh()->notification_sent_at);
    }

    public function test_resend_skips_spam(): void
    {
        Intake::factory()->undelivered()->create(['status' => 'spam']);

        $this->artisan('intake:resend')
            ->expectsOutputToContain('Nothing to retry')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_resend_by_id_needs_force_for_emails_that_already_went_out(): void
    {
        $intake = Intake::factory()->create();

        $this->artisan('intake:resend', ['ids' => [$intake->id]])
            ->expectsOutputToContain('Add --force')
            ->assertSuccessful();

        Mail::assertNothingSent();

        $this->artisan('intake:resend', ['ids' => [$intake->id], '--force' => true, '--only' => 'notification'])
            ->assertSuccessful();

        Mail::assertSent(IntakeNotification::class, 1);
        Mail::assertNotSent(IntakeConfirmation::class);
    }

    public function test_resend_reports_failures_and_exits_non_zero(): void
    {
        Intake::factory()->undelivered()->create();
        config(['mail.intake_notify_address' => '']);

        $this->artisan('intake:resend', ['--only' => 'notification'])
            ->expectsOutputToContain('INTAKE_NOTIFY_ADDRESS')
            ->assertFailed();
    }

    public function test_resend_rejects_an_unknown_email_name(): void
    {
        $this->artisan('intake:resend', ['--only' => 'invoice'])->assertExitCode(2);
    }

    public function test_list_shows_failed_intakes(): void
    {
        Intake::factory()->create(['name' => 'Fine Person']);
        Intake::factory()->undelivered()->create(['name' => 'Bounced Person']);

        $this->artisan('intake:list', ['--failed' => true])
            ->expectsOutputToContain('Bounced Person')
            ->doesntExpectOutputToContain('Fine Person')
            ->assertSuccessful();
    }

    public function test_admin_user_creates_then_resets_an_account(): void
    {
        $this->artisan('admin:user', ['email' => 'me@example.com'])
            ->expectsQuestion('Password', 'first-password-123')
            ->expectsOutputToContain('Created admin me@example.com.')
            ->assertSuccessful();

        $this->artisan('admin:user', ['email' => 'me@example.com'])
            ->expectsQuestion('Password', 'second-password-456')
            ->expectsOutputToContain('Reset the password for me@example.com.')
            ->assertSuccessful();

        $user = User::sole();
        $this->assertTrue(Hash::check('second-password-456', $user->password));
    }
}
