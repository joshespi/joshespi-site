<?php

namespace Tests\Feature;

use App\Livewire\IntakeForm;
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
        'service'  => 'Not sure / other',
        'budget'   => '$500–$1,500',
        'timeline' => 'Flexible',
        'message'  => 'My WordPress site loads very slowly and I need help fixing the performance issues.',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('intake:127.0.0.1');
    }

    public function test_valid_submit_sends_notification_email(): void
    {
        Mail::fake();
        config(['mail.intake_notify_address' => 'notify@example.com']);

        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $this->baseData['service'])
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertSet('submitted', true);

        Mail::assertSent(IntakeNotification::class, fn ($mail) => $mail->hasTo('notify@example.com'));
    }

    public function test_wp_service_requires_admin_access_answer(): void
    {
        $wpService = array_key_first(IntakeForm::serviceMenu()['WordPress — Fixed Price']);

        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $wpService)
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasErrors(['wpAdminAccess' => 'required']);
    }

    public function test_wp_security_audit_also_requires_admin_access(): void
    {
        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', 'WordPress Security Audit')
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasErrors(['wpAdminAccess' => 'required']);
    }

    public function test_custom_dev_service_requires_starting_point(): void
    {
        $devService = array_key_first(IntakeForm::serviceMenu()['Custom Development']);

        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $devService)
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasErrors(['startingPoint' => 'required']);
    }

    public function test_rate_limit_blocks_after_five_attempts(): void
    {
        Mail::fake();
        config(['mail.intake_notify_address' => 'notify@example.com']);

        $component = Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $this->baseData['service'])
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message']);

        for ($i = 0; $i < 5; $i++) {
            $component->call('submit');
            $component->set('submitted', false); // reset for next attempt
        }

        $component->call('submit')->assertHasErrors(['email']);
        Mail::assertSent(IntakeNotification::class, 5);
    }

    public function test_submitted_flag_false_before_submit(): void
    {
        Livewire::test(IntakeForm::class)
            ->assertSet('submitted', false);
    }

    public function test_name_is_required(): void
    {
        Livewire::test(IntakeForm::class)
            ->set('name', '')
            ->set('email', $this->baseData['email'])
            ->set('service', $this->baseData['service'])
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_email_must_be_valid(): void
    {
        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', 'not-an-email')
            ->set('service', $this->baseData['service'])
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_budget_is_required(): void
    {
        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $this->baseData['service'])
            ->set('budget', '')
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasErrors(['budget' => 'required']);
    }

    public function test_timeline_is_required(): void
    {
        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $this->baseData['service'])
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', '')
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertHasErrors(['timeline' => 'required']);
    }

    public function test_message_must_be_at_least_20_characters(): void
    {
        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $this->baseData['service'])
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', 'Too short')
            ->call('submit')
            ->assertHasErrors(['message' => 'min']);
    }

    public function test_empty_notify_address_does_not_complete_submission(): void
    {
        Mail::fake();
        config(['mail.intake_notify_address' => '']);

        Livewire::test(IntakeForm::class)
            ->set('name', $this->baseData['name'])
            ->set('email', $this->baseData['email'])
            ->set('service', $this->baseData['service'])
            ->set('budget', $this->baseData['budget'])
            ->set('timeline', $this->baseData['timeline'])
            ->set('message', $this->baseData['message'])
            ->call('submit')
            ->assertSet('submitted', false);

        Mail::assertNotSent(IntakeNotification::class);
    }
}
