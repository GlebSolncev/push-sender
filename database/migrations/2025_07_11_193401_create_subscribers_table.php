<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();

            $table->string('auth_token', 255);
            $table->string('public_key', 255);
            $table->string('endpoint', 512);
            $table->string('geo');
            $table->string('platform');
            $table->string('country');

            $table->unsignedInteger('geo_id');
            $table->unsignedInteger('platform_id');
            $table->unsignedInteger('country_id');

            $table->boolean('is_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
