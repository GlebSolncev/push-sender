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
        Schema::create('statistic_notifications', function (Blueprint $table) {
            $table->foreignId('messages_id')->constrained()->on('messages')->cascadeOnDelete();
            $table->integer('type')->default(0)->index();

            $table->bigInteger('count')->default(0);

            $table->primary(['messages_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistic_notifications');
    }
};
