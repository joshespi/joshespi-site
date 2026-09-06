<?php

namespace Tests\Feature;

use App\Livewire\IntakeForm;
use App\Mail\IntakeConfirmation;
use App\Mail\IntakeNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class IntakeFormTest extends TestCase
{
    // Uses "Not sure / other" so no conditional fields are required
    private array $baseData = [
        'name'     => 'Jane Client',
        'email'    => 'jane@example.com',
        'service'  => IntakeForm::OTHER_SERVICE,
        'budget'   => '$500–$1,500',
        'timeline' => 'Flexible',
        'message'  => 'My WordPress site loads very slowly and I need help fixing the performance issues.',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Derived, not hardcoded: a literal '127.0.0.1' silently stops clearing
        // anything the day the test client reports a different address.
        RateLimiter::clear('intake:' . request()->ip());

        // submit() refuses to do anything without this, so every test needs it.
        // The tests that care about it missing override it themselves.
        config(['mail.intake_notify_address' => 'notify@example.com']);
    }

    /** A filled-in form. Pass overrides for the field under test. */
    private function form(array $overrides = [])
    {
        $component = Livewire::test(IntakeForm::class);

        foreach (array_merge($this->baseData, $overrides) as $field => $value) {
            $component->set($field, $value);
        }

        return $component;
    }

    public function test_valid_submit_sends_notification_email(): void
    {
        Mail::fake();

        $this->form()
            ->call('submit')
            ->assertSet('submitted', true);

        Mail::assertSent(IntakeNotification::class, fn ($mail) => $mail->hasTo('notify@example.com'));
    }

    public function test_valid_submit_sends_confirmation_to_the_client(): void
    {
        Mail::fake();

        $this->form()
            ->call('submit')
            ->assertSet('submitted', true);

        Mail::assertSent(IntakeConfirmation::class, fn ($mail) => $mail->hasTo('jane@example.com'));
    }

    public function test_wp_service_requires_admin_access_answer(): void
    {
        $wpService = array_key_first(IntakeForm::serviceMenu()['WordPress — Fixed Price']);

        $this->form(['service' => $wpService])
            ->call('submit')
            ->assertHasErrors(['wpAdminAccess' => 'required']);
    }

    public function test_wp_security_audit_also_requires_admin_access(): void
    {
        $this->form(['service' => 'WordPress Security Audit'])
            ->call('submit')
            ->assertHasErrors(['wpAdminAccess' => 'required']);
    }

    public function test_custom_dev_service_requires_starting_point(): void
    {
        $devService = array_key_first(IntakeForm::serviceMenu()['Custom Development']);

        $this->form(['service' => $devService])
            ->call('submit')
            ->assertHasErrors(['startingPoint' => 'required']);
    }

    public function test_submitted_flag_false_before_submit(): void
    {
        Livewire::test(IntakeForm::class)->assertSet('submitted', false);
    }

    public function test_name_is_required(): void
    {
        $this->form(['name' => ''])
            ->call('submit')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_email_must_be_valid(): void
    {
        $this->form(['email' => 'not-an-email'])
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_budget_is_required(): void
    {
        $this->form(['budget' => ''])
            ->call('submit')
            ->assertHasErrors(['budget' => 'required']);
    }

    public function test_timeline_is_required(): void
    {
        $this->form(['timeline' => ''])
            ->call('submit')
            ->assertHasErrors(['timeline' => 'required']);
    }

    public function test_message_must_be_at_least_20_characters(): void
    {
        $this->form(['message' => 'Too short'])
            ->call('submit')
            ->assertHasErrors(['message' => 'min']);
    }

    public function test_service_must_be_one_of_the_listed_options(): void
    {
        Mail::fake();

        $this->form(['service' => 'Wire me $10,000'])
            ->call('submit')
            ->assertHasErrors(['service' => 'in'])
            ->assertSet('submitted', false);

        Mail::assertNothingSent();
    }

    public function test_budget_must_be_one_of_the_listed_options(): void
    {
        $this->form(['budget' => 'A trillion dollars'])
            ->call('submit')
            ->assertHasErrors(['budget' => 'in']);
    }

    public function test_timeline_must_be_one_of_the_listed_options(): void
    {
        $this->form(['timeline' => 'Yesterday'])
            ->call('submit')
            ->assertHasErrors(['timeline' => 'in']);
    }

    public function test_every_menu_option_passes_the_service_allowlist(): void
    {
        // Guards the allowlist against drifting away from the rendered <select>.
        $menuValues = array_merge(
            ...array_map('array_keys', array_values(IntakeForm::serviceMenu()))
        );

        foreach ($menuValues as $service) {
            $this->assertContains(
                $service,
                IntakeForm::serviceValues(),
                "Menu option [{$service}] is rendered but would fail validation."
            );
        }
    }

    public function test_honeypot_submission_is_silently_discarded(): void
    {
        Mail::fake();

        // Reports success so the bot has nothing to tune against.
        $this->form(['website' => 'http://spam.example'])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        Mail::assertNothingSent();
    }

    public function test_rate_limit_blocks_after_five_attempts(): void
    {
        Mail::fake();

        $component = $this->form();

        for ($i = 0; $i < 5; $i++) {
            $component->call('submit');
            $component->set('submitted', false); // reset for next attempt
        }

        $component->call('submit')->assertHasErrors('form');
        Mail::assertSent(IntakeNotification::class, 5);
    }

    public function test_empty_notify_address_does_not_complete_submission(): void
    {
        Mail::fake();
        config(['mail.intake_notify_address' => '']);

        $this->form()
            ->call('submit')
            ->assertHasErrors('form')
            ->assertSet('submitted', false);

        Mail::assertNotSent(IntakeNotification::class);
    }

    public function test_missing_notify_address_does_not_consume_rate_limit_attempts(): void
    {
        Mail::fake();
        config(['mail.intake_notify_address' => '']);

        $component = $this->form();

        // Six blocked attempts against a misconfigured inbox. The operator's
        // mistake must not spend the visitor's five tries.
        for ($i = 0; $i < 6; $i++) {
            $component->call('submit')->assertSet('submitted', false);
        }

        config(['mail.intake_notify_address' => 'notify@example.com']);

        $component->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        Mail::assertSent(IntakeNotification::class, 1);
    }

    public function test_failed_validation_does_not_consume_rate_limit_attempts(): void
    {
        Mail::fake();

        $component = $this->form(['message' => 'Too short']);

        // Six fumbled submissions — a typo is not a submission.
        for ($i = 0; $i < 6; $i++) {
            $component->call('submit')->assertHasErrors(['message' => 'min']);
        }

        $component
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        Mail::assertSent(IntakeNotification::class, 1);
    }
}
