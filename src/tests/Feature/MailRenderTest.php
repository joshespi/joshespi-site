<?php

namespace Tests\Feature;

use App\Mail\IntakeConfirmation;
use App\Mail\IntakeNotification;
use App\Models\Intake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_a_summary_that_links_to_the_admin(): void
    {
        $intake = Intake::factory()->create([
            'name'     => 'Jane Client',
            'email'    => 'jane@example.com',
            'service'  => 'Speed Optimization ($349)',
            'timeline' => 'Flexible',
            'message'  => 'The site is slow.',
        ]);

        $html = (new IntakeNotification($intake))->render();

        $this->assertStringContainsString("New intake #{$intake->id}: Speed Optimization", $html);
        $this->assertStringContainsString('jane@example.com', $html);
        $this->assertStringContainsString('Flexible', $html);
        $this->assertStringContainsString(route('admin.intakes.show', $intake), $html);
        // The message is read in the admin, not in the inbox.
        $this->assertStringNotContainsString('The site is slow.', $html);
    }

    public function test_confirmation_carries_no_free_text_from_the_submission(): void
    {
        // It goes to whatever address was typed in. Anything echoed here is
        // a stranger's words sent from this domain.
        $intake = Intake::factory()->create([
            'name'     => 'NAME-MARKER',
            'url'      => 'https://url-marker.example',
            'service'  => 'Speed Optimization ($349)',
            'timeline' => 'Flexible',
            'message'  => 'BODY-MARKER visit my spam site',
            'details'  => ['Hosting provider' => 'DETAIL-MARKER'],
        ]);

        $html = (new IntakeConfirmation($intake))->render();

        $this->assertStringContainsString('Speed Optimization', $html);
        $this->assertStringContainsString('Flexible', $html);
        foreach (['NAME-MARKER', 'url-marker', 'BODY-MARKER', 'DETAIL-MARKER'] as $marker) {
            $this->assertStringNotContainsString($marker, $html);
        }
    }

    public function test_notification_values_are_escaped_not_injected(): void
    {
        $intake = Intake::factory()->create([
            'name'   => '<script>alert(1)</script>',
            'budget' => '<img src=x onerror=alert(1)>',
        ]);

        $html = (new IntakeNotification($intake))->render();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
    }
}
