<?php

namespace App\Livewire;

use App\Mail\IntakeNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class IntakeForm extends Component
{
    private const WP_SECURITY_AUDIT = 'WordPress Security Audit';
    private const WP_MIGRATION      = 'Site Migration ($249)';

    public string $name = '';
    public string $email = '';
    public string $url = '';
    public string $service = '';
    public string $budget = '';
    public string $timeline = '';
    public string $wpAdminAccess = '';
    public string $hostingProvider = '';
    public string $currentHost = '';
    public string $startingPoint = '';
    public string $integrations = '';
    public string $stackLanguage = '';
    public string $message = '';
    public bool $submitted = false;

    protected array $messages = [
        'message.min' => 'Please give a bit more detail — at least 20 characters.',
    ];

    // Keys = submitted values, values = display labels. Group keys = optgroup labels.
    public static function serviceMenu(): array
    {
        return [
            'WordPress — Fixed Price' => [
                'Stuck Site Diagnostic ($149)'          => 'Stuck Site Diagnostic — $149',
                'Speed Optimization ($349)'             => 'Speed Optimization — $349',
                'Hacked Site Cleanup ($499)'            => 'Hacked Site Cleanup — $499',
                self::WP_MIGRATION                      => 'Site Migration — $249',
                'Maintenance Retainer — Basic ($99/mo)' => 'Maintenance Retainer Basic — $99/mo',
                'Maintenance Retainer — Plus ($199/mo)' => 'Maintenance Retainer Plus — $199/mo',
            ],
            'Custom Development' => [
                'Custom WordPress Plugin'   => 'Custom WordPress Plugin',
                'Laravel Application'       => 'Laravel Application',
                'API Integration'           => 'API Integration',
                'Internal Tool / Dashboard' => 'Internal Tool / Dashboard',
            ],
            'DevOps & Security' => [
                'Dockerize an Application'   => 'Dockerize an Application',
                self::WP_SECURITY_AUDIT      => 'WordPress Security Audit',
                'Server Setup & Hardening'   => 'Server Setup & Hardening',
                'CI/CD Pipeline'             => 'CI/CD Pipeline',
            ],
        ];
    }

    public function isWpService(): bool
    {
        // WordPress Security Audit is listed under DevOps but needs WP access fields
        return array_key_exists($this->service, self::serviceMenu()['WordPress — Fixed Price'])
            || $this->service === self::WP_SECURITY_AUDIT;
    }

    public function isMigration(): bool
    {
        return $this->service === self::WP_MIGRATION;
    }

    public function isApiIntegration(): bool
    {
        return $this->service === 'API Integration';
    }

    public function isCustomDevService(): bool
    {
        return array_key_exists($this->service, self::serviceMenu()['Custom Development']);
    }

    public function isDevOpsService(): bool
    {
        // WordPress Security Audit uses WP field set instead
        return array_key_exists($this->service, self::serviceMenu()['DevOps & Security'])
            && $this->service !== self::WP_SECURITY_AUDIT;
    }

    public function submit(): void
    {
        $key = 'intake:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Too many submissions — please wait a few minutes before trying again.');
            return;
        }

        RateLimiter::hit($key, 300);

        $this->validate($this->validationRules());

        $to = config('mail.intake_notify_address');
        abort_if(empty($to), 500, 'Intake notification address not configured.');

        Mail::to($to)->send(new IntakeNotification(
            name: $this->name,
            email: $this->email,
            service: $this->service,
            body: $this->message,
            details: $this->extraDetails(),
        ));

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.intake-form');
    }

    private function validationRules(): array
    {
        $rules = [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150',
            'url'      => 'nullable|string|max:255',
            'service'  => 'required|string',
            'budget'   => 'required|string',
            'timeline' => 'required|string',
            'message'  => 'required|string|min:20|max:3000',
        ];

        if ($this->isWpService()) {
            $rules['wpAdminAccess'] = 'required|string';
        }

        if ($this->isCustomDevService()) {
            $rules['startingPoint'] = 'required|string';
        }

        return $rules;
    }

    private function extraDetails(): array
    {
        $details = [];

        if ($this->url) {
            $details['Site URL'] = $this->url;
        }

        if ($this->isWpService()) {
            $details['WP admin access'] = $this->wpAdminAccess;
            if ($this->hostingProvider) {
                $details['Hosting provider'] = $this->hostingProvider;
            }
        }

        if ($this->isMigration() && $this->currentHost) {
            $details['Current host'] = $this->currentHost;
        }

        if ($this->isCustomDevService()) {
            $details['Starting point'] = $this->startingPoint;
            if ($this->integrations) {
                $details['Integrations / APIs'] = $this->integrations;
            }
        }

        if ($this->isDevOpsService() && $this->stackLanguage) {
            $details['Stack / language'] = $this->stackLanguage;
        }

        $details['Budget']   = $this->budget;
        $details['Timeline'] = $this->timeline;

        return $details;
    }
}
