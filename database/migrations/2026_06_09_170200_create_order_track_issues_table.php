<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_track_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_track_id')->constrained()->cascadeOnDelete();
            $table->string('full_name', 120);
            $table->string('email', 180);
            $table->string('phone', 40)->nullable();
            $table->string('issue_type', 60);
            $table->text('description');
            $table->string('status', 40)->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['order_track_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_track_issues');
    }
};
