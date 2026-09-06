<?php

namespace Tests\Feature;

use App\Mail\IntakeConfirmation;
use App\Mail\IntakeNotification;
use Tests\TestCase;

class MailRenderTest extends TestCase
{
    private array $details = [
        'Site URL' => 'https://example.com',
        'Budget'   => '$500–$1,500',
        'Timeline' => 'Flexible',
    ];

    public function test_notification_renders_with_all_details(): void
    {
        $html = (new IntakeNotification(
            name: 'Jane Client',
            email: 'jane@example.com',
            service: 'Speed Optimization ($349)',
            body: 'The site is slow.',
            details: $this->details,
        ))->render();

        $this->assertStringContainsString('New intake: Speed Optimization', $html);
        $this->assertStringContainsString('jane@example.com', $html);
        $this->assertStringContainsString('Flexible', $html);
        $this->assertStringContainsString('The site is slow.', $html);
    }

    public function test_confirmation_renders_with_all_details(): void
    {
        $html = (new IntakeConfirmation(
            name: 'Jane Client',
            service: 'Speed Optimization ($349)',
            body: 'The site is slow.',
            details: $this->details,
        ))->render();

        $this->assertStringContainsString('Thanks, Jane Client', $html);
        $this->assertStringContainsString('Flexible', $html);
        $this->assertStringContainsString('The site is slow.', $html);
    }

    public function test_confirmation_values_are_escaped_not_injected(): void
    {
        $html = (new IntakeConfirmation(
            name: '<script>alert(1)</script>',
            service: 'Not sure / other',
            body: 'plain',
            details: ['Budget' => '<img src=x onerror=alert(1)>'],
        ))->render();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
    }

    public function test_notification_values_are_escaped_not_injected(): void
    {
        // This one renders everything the confirmation does plus the submitted
        // email address, and it is the message that lands in my inbox.
        $html = (new IntakeNotification(
            name: '<script>alert(1)</script>',
            email: 'jane@example.com',
            service: 'Not sure / other',
            body: '<b>bold</b> and <script>alert(2)</script>',
            details: ['Budget' => '<img src=x onerror=alert(1)>'],
        ))->render();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<script>alert(2)</script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringNotContainsString('<b>bold</b>', $html);
    }
}
