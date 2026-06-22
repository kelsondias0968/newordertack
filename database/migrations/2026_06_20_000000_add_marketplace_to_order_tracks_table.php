<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_tracks', function (Blueprint $table) {
            $table->string('marketplace', 40)->default('takealot')->after('preferred_locale');
        });
    }

    public function down(): void
    {
        Schema::table('order_tracks', function (Blueprint $table) {
            $table->dropColumn('marketplace');
        });
    }
};
