<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('url')->nullable();
            $table->string('service');
            $table->string('budget');
            $table->string('timeline');
            $table->text('message');
            // The service-specific answers (WP admin access, starting point...),
            // already labelled for display. They vary by service, so no columns.
            $table->json('details')->nullable();
            $table->string('status', 20)->default('new')->index();

            // One pair per email. sent_at null + error set = failed;
            // both null = never attempted. intake:resend reads these.
            $table->timestamp('notification_sent_at')->nullable();
            $table->text('notification_error')->nullable();
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->text('confirmation_error')->nullable();

            $table->timestamps();
        });

        Schema::create('intake_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intake_notes');
        Schema::dropIfExists('intakes');
    }
};
