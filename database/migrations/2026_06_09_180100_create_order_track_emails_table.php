<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_track_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_track_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_track_stage_id')->nullable()->constrained()->nullOnDelete();
            $table->string('notification_type', 40);
            $table->string('stage_key', 40)->nullable();
            $table->string('locale', 8)->default('en');
            $table->string('status', 20)->default('pending');
            $table->string('mailer', 50)->nullable();
            $table->string('recipient_email', 180);
            $table->string('recipient_name', 120)->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('subject');
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['order_track_id', 'status']);
            $table->index(['notification_type', 'stage_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_track_emails');
    }
};
