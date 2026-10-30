<?php

namespace Tests\Feature;

use App\Mail\IntakeConfirmation;
use App\Mail\IntakeNotification;
use App\Mail\TestMessage;
use App\Models\Intake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
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

    public function test_mail_test_sends_to_the_given_address(): void
    {
        $this->artisan('mail:test', ['to' => 'me@example.com'])
            ->expectsOutputToContain('Sent.')
            ->assertSuccessful();

        Mail::assertSent(TestMessage::class, fn ($mail) => $mail->hasTo('me@example.com'));
    }

    public function test_mail_test_falls_back_to_the_notify_address(): void
    {
        $this->artisan('mail:test')->assertSuccessful();

        Mail::assertSent(TestMessage::class, fn ($mail) => $mail->hasTo('notify@example.com'));
    }

    public function test_mail_test_says_when_the_log_mailer_did_not_deliver(): void
    {
        config(['mail.default' => 'log']);

        $this->artisan('mail:test')
            ->expectsOutputToContain('not delivered')
            ->assertSuccessful();
    }

    public function test_mail_test_reports_a_send_failure_and_its_cause(): void
    {
        Mail::shouldReceive('to->send')
            ->andThrow(new RuntimeException('Send failed', 0, new RuntimeException('Connection refused')));

        $this->artisan('mail:test', ['to' => 'me@example.com'])
            ->expectsOutputToContain('Send failed')
            ->expectsOutputToContain('Connection refused')
            ->assertFailed();
    }

    public function test_mail_test_needs_a_recipient(): void
    {
        config(['mail.intake_notify_address' => '']);

        $this->artisan('mail:test')->assertExitCode(2);

        Mail::assertNothingSent();
    }

    public function test_mail_test_is_scheduled_weekly(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('mail:test')
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
