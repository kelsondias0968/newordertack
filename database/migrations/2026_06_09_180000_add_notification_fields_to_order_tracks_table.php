<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_tracks', function (Blueprint $table) {
            $table->string('preferred_locale', 8)->default('en')->after('customer_phone');
            $table->json('notification_cc')->nullable()->after('preferred_locale');
            $table->json('notification_bcc')->nullable()->after('notification_cc');
        });
    }

    public function down(): void
    {
        Schema::table('order_tracks', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_locale',
                'notification_cc',
                'notification_bcc',
            ]);
        });
    }
};
