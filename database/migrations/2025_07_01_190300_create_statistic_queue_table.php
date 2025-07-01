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

            $table->tinyInteger('status')->default(0)->comment('when status `start` counter reset');

            $table->integer('success')->default(0);
            $table->integer('failed')->default(0);
            $table->integer('total')->default(0);

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
