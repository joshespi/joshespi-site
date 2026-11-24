<?php

namespace Tests\Feature;

use App\Models\Intake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrackerPushTest extends TestCase
{
    use RefreshDatabase;

    public function test_push_sends_the_lead_with_the_token_and_records_success(): void
    {
        config(['services.tracker.url' => 'http://tracker.test/', 'services.tracker.token' => 'ttk_abc']);
        Http::fake(['tracker.test/*' => Http::response(['id' => 1, 'created' => true], 201)]);
        $intake = Intake::factory()->create(['details' => ['Admin access' => 'Yes']]);

        $this->assertTrue($intake->pushToTracker());

        Http::assertSent(fn (Request $r) => $r->url() === 'http://tracker.test/api/v1/intakes'
            && $r->hasHeader('Authorization', 'Bearer ttk_abc')
            && $r['external_id'] === 'joshespi-site-' . $intake->id
            && $r['email'] === $intake->email
            && $r['details'] === ['Admin access' => 'Yes']);
        $this->assertNotNull($intake->fresh()->tracker_sent_at);
        $this->assertNull($intake->fresh()->tracker_error);
    }

    public function test_a_failing_tracker_never_throws_and_is_recorded_for_retry(): void
    {
        config(['services.tracker.url' => 'http://tracker.test', 'services.tracker.token' => 'ttk_abc']);
        Http::fake(['tracker.test/*' => Http::response(['message' => 'down'], 500)]);
        $intake = Intake::factory()->create();

        $this->assertFalse($intake->pushToTracker());

        $this->assertNull($intake->fresh()->tracker_sent_at);
        $this->assertNotNull($intake->fresh()->tracker_error);
    }

    public function test_nothing_is_sent_when_the_tracker_is_not_configured(): void
    {
        config(['services.tracker.url' => '', 'services.tracker.token' => '']);
        Http::fake();
        $intake = Intake::factory()->create();

        $this->assertFalse($intake->pushToTracker());

        Http::assertNothingSent();
        $this->assertNull($intake->fresh()->tracker_error);
    }

    public function test_push_command_retries_only_unsent_non_spam_intakes(): void
    {
        config(['services.tracker.url' => 'http://tracker.test', 'services.tracker.token' => 'ttk_abc']);
        Http::fake(['tracker.test/*' => Http::response(['id' => 1], 201)]);
        $unsent = Intake::factory()->create();
        Intake::factory()->create(['tracker_sent_at' => now()]);
        Intake::factory()->create(['status' => 'spam']);

        $this->artisan('intake:push')->expectsOutputToContain('1 pushed, 0 failed.')->assertSuccessful();

        Http::assertSentCount(1);
        $this->assertNotNull($unsent->fresh()->tracker_sent_at);
    }
}
