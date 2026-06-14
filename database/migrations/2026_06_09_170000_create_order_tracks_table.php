<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 40)->unique();
            $table->string('order_number', 100)->unique();
            $table->string('customer_name', 120)->nullable();
            $table->string('customer_email', 180)->nullable();
            $table->string('customer_phone', 40)->nullable();
            $table->string('product_name');
            $table->text('product_image_url')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('current_stage', 40)->index();
            $table->boolean('auto_progress')->default(true);
            $table->timestamp('placed_at');
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_stage_change_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracks');
    }
};
