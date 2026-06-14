<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_track_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_track_id')->constrained()->cascadeOnDelete();
            $table->string('stage_key', 40);
            $table->unsignedSmallInteger('position');
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('duration_hours')->default(0);
            $table->timestamp('planned_for_at');
            $table->timestamp('reached_at')->nullable();
            $table->boolean('manual_override')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['order_track_id', 'stage_key']);
            $table->index(['order_track_id', 'position']);
            $table->index('planned_for_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_track_stages');
    }
};
