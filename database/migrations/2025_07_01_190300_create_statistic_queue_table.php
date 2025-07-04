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
        Schema::create('statistic_queue', function (Blueprint $table) {
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('status')->default(0)->unsigned();

            $table->unsignedBigInteger('success')->default(0);
            $table->unsignedBigInteger('failed')->default(0);
            $table->unsignedBigInteger('total')->default(0);

            $table->timestamps();
            $table->primary('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistic_queue');
    }
};
