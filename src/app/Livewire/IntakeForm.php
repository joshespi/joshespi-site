<?php

namespace App\Livewire;

use App\Mail\IntakeConfirmation;
use App\Mail\IntakeNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;

class IntakeForm extends Component
{
    private const WP_SECURITY_AUDIT = 'WordPress Security Audit';
    private const WP_MIGRATION      = 'Site Migration ($249)';
    private const API_INTEGRATION   = 'API Integration';

    public const OTHER_SERVICE = 'Not sure / other';

    // The option lists the <select>s render and the validator checks against.
    // Kept here so a value can never appear in the markup without being
    // accepted, or be accepted without appearing in the markup.
    public const BUDGETS = [
        'Under $500',
        '$500–$1,500',
        '$1,500–$5,000',
        '$5,000+',
        'Not sure',
    ];

    public const TIMELINES = [
        'ASAP',
        'Within 2 weeks',
        'Within a month',
        'Flexible',
    ];

    public const WP_ADMIN_ACCESS = [
        'Yes',
        'Not yet',
        'Need help getting it',
        'No',
    ];

    public const STARTING_POINTS = [
        'Existing code to extend',
        'Starting from scratch',
    ];

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

    // Honeypot. Hidden from people, irresistible to form-stuffing bots.
    public string $website = '';

    protected array $messages = [
        'message.min' => 'Please give a bit more detail — at least 20 characters.',
        'service.in'  => 'Please pick one of the listed services.',
        'budget.in'   => 'Please pick one of the listed budget ranges.',
        'timeline.in' => 'Please pick one of the listed timelines.',
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
                self::API_INTEGRATION       => 'API Integration',
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

    /** Every value the service <select> can legitimately submit. */
    public static function serviceValues(): array
    {
        $grouped = array_map('array_keys', array_values(self::serviceMenu()));

        return [...array_merge(...$grouped), self::OTHER_SERVICE];
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
        return $this->service === self::API_INTEGRATION;
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
        // A browser never fills a hidden field. Report success so the bot has
        // no signal to tune against, and send nothing.
        if ($this->website !== '') {
            $this->submitted = true;

            return;
        }

        // Checked before the rate limiter is touched: an operator's missing env
        // var must not burn the visitor's five attempts.
        $to = config('mail.intake_notify_address');
        if (empty($to)) {
            Log::error('INTAKE_NOTIFY_ADDRESS is not configured — intake submission dropped.');
            $this->addError('form', 'The form is temporarily unavailable. Please try again later.');

            return;
        }

        $key = 'intake:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('form', 'Too many submissions — please wait a few minutes before trying again.');

            return;
        }

        // Validate first — a failed validation is a typo, not a submission,
        // and should not burn one of the five attempts.
        $this->validate($this->validationRules());

        RateLimiter::hit($key, 300);

        $details = $this->extraDetails();

        Mail::to($to)->send(new IntakeNotification(
            name: $this->name,
            email: $this->email,
            service: $this->service,
            body: $this->message,
            details: $details,
        ));

        // Client receipt. Never let a bad recipient address cost us the
        // submission — the notification above is the one that matters.
        try {
            Mail::to($this->email)->send(new IntakeConfirmation(
                name: $this->name,
                service: $this->service,
                body: $this->message,
                details: $details,
            ));
        } catch (\Throwable $e) {
            Log::warning('Intake confirmation email failed', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }

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
            'service'  => ['required', 'string', Rule::in(self::serviceValues())],
            'budget'   => ['required', 'string', Rule::in(self::BUDGETS)],
            'timeline' => ['required', 'string', Rule::in(self::TIMELINES)],
            'message'  => 'required|string|min:20|max:3000',
        ];

        if ($this->isWpService()) {
            $rules['wpAdminAccess'] = ['required', Rule::in(self::WP_ADMIN_ACCESS)];
        }

        if ($this->isCustomDevService()) {
            $rules['startingPoint'] = ['required', Rule::in(self::STARTING_POINTS)];
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
