<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // Same convention as the email columns: sent_at null + error set = failed,
            // both null = never attempted (or the tracker isn't configured).
            $table->timestamp('tracker_sent_at')->nullable();
            $table->text('tracker_error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            $table->dropColumn(['tracker_sent_at', 'tracker_error']);
        });
    }
};
