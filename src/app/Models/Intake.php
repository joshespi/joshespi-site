<?php

namespace App\Models;

use App\Enums\IntakeEmail;
use App\Enums\IntakeStatus;
use App\Mail\IntakeConfirmation;
use App\Mail\IntakeNotification;
use Database\Factories\IntakeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class Intake extends Model
{
    /** @use HasFactory<IntakeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'url',
        'service',
        'budget',
        'timeline',
        'message',
        'details',
        'status',
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    protected function casts(): array
    {
        return [
            'details'              => 'array',
            'status'               => IntakeStatus::class,
            'notification_sent_at' => 'datetime',
            'confirmation_sent_at' => 'datetime',
            'tracker_sent_at'      => 'datetime',
        ];
    }

    public function notes(): HasMany
    {
        return $this->hasMany(IntakeNote::class)->latest('id');
    }

    /**
     * Intakes with an email that never went out. Spam is left out: its
     * confirmation usually bounces forever, and nobody wants it retried.
     * isUndelivered() is the same rule for a single row.
     */
    public function scopeUndelivered(Builder $query): void
    {
        $query->where('status', '!=', IntakeStatus::Spam)
            ->where(function (Builder $q) {
                foreach (IntakeEmail::cases() as $email) {
                    $q->orWhereNull($email->sentAtColumn());
                }
            });
    }

    /** Intakes the tracker has not received. Spam is left out, as in scopeUndelivered(). */
    public function scopeUntracked(Builder $query): void
    {
        $query->where('status', '!=', IntakeStatus::Spam)->whereNull('tracker_sent_at');
    }

    public function isUndelivered(): bool
    {
        return $this->status !== IntakeStatus::Spam
            && collect(IntakeEmail::cases())->contains(fn (IntakeEmail $email) => ! $this->wasSent($email));
    }

    public function sentAt(IntakeEmail $email): ?Carbon
    {
        return $this->{$email->sentAtColumn()};
    }

    public function wasSent(IntakeEmail $email): bool
    {
        return $this->sentAt($email) !== null;
    }

    public function emailError(IntakeEmail $email): ?string
    {
        return $this->{$email->errorColumn()};
    }

    /** The site URL as a link target, or null when it isn't a plain web URL. */
    public function safeUrl(): ?string
    {
        // Free text from a stranger. Anything but http(s) — javascript: above
        // all — must never reach an href.
        return Str::isUrl($this->url, ['http', 'https']) ? $this->url : null;
    }

    /**
     * Send one of the two emails and record how it went on the row. Never
     * throws for a mail failure: the intake is already saved, and a dead SMTP
     * relay must not turn into an error page for the visitor. The recorded
     * error is what intake:resend and the admin page work from.
     */
    public function deliver(IntakeEmail $email): bool
    {
        try {
            match ($email) {
                IntakeEmail::Notification => Mail::to($this->notifyAddress())->send(new IntakeNotification($this)),
                IntakeEmail::Confirmation => Mail::to($this->email)->send(new IntakeConfirmation($this)),
            };
        } catch (Throwable $e) {
            // A client's receipt bouncing off a typo'd address is routine; my
            // own notification failing means a lead I might not know about.
            Log::log(
                $email === IntakeEmail::Notification ? 'error' : 'warning',
                "Intake #{$this->id}: {$email->value} email failed",
                ['error' => $e->getMessage()],
            );
            $this->forceFill([$email->errorColumn() => Str::limit($e->getMessage(), 1000)])->save();

            return false;
        }

        $this->forceFill([$email->sentAtColumn() => now(), $email->errorColumn() => null])->save();

        return true;
    }

    public static function trackerConfigured(): bool
    {
        return filled(config('services.tracker.url')) && filled(config('services.tracker.token'));
    }

    /**
     * Hand this lead to Tradesman Tracker, where it waits for triage. Like deliver(), it never
     * throws: the intake is already saved, and a down tracker must not cost the visitor a
     * submission. The tracker de-duplicates on external_id, so a retry after a lost response
     * is safe.
     */
    public function pushToTracker(): bool
    {
        if (! self::trackerConfigured()) {
            return false;
        }

        try {
            Http::withToken(config('services.tracker.token'))
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout(5)
                ->post(rtrim(config('services.tracker.url'), '/') . '/api/v1/intakes', [
                    'external_id' => 'joshespi-site-' . $this->id,
                    'name'        => $this->name,
                    'email'       => $this->email,
                    'url'         => $this->url,
                    'service'     => $this->service,
                    'budget'      => $this->budget,
                    'timeline'    => $this->timeline,
                    'message'     => $this->message,
                    'details'     => $this->details ?: null,
                ])
                ->throw();
        } catch (Throwable $e) {
            Log::warning("Intake #{$this->id}: tracker push failed", ['error' => $e->getMessage()]);
            $this->forceFill(['tracker_error' => Str::limit($e->getMessage(), 1000)])->save();

            return false;
        }

        $this->forceFill(['tracker_sent_at' => now(), 'tracker_error' => null])->save();

        return true;
    }

    private function notifyAddress(): string
    {
        return config('mail.intake_notify_address')
            ?: throw new RuntimeException('INTAKE_NOTIFY_ADDRESS is not configured.');
    }
}
