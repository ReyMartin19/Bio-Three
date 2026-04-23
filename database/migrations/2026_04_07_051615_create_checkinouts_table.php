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
        Schema::create('checkinout', function (Blueprint $table) {
            $table->integer('Logid')->autoIncrement();
            $table->string('Userid')->nullable();
            $table->dateTime('CheckTime')->nullable();
            $table->string('CheckType', 50)->nullable();
            $table->integer('Sensorid')->nullable();
            $table->integer('WorkType')->nullable();
            $table->integer('AttFlag')->nullable();
            $table->boolean('Checked')->default(0);
            $table->boolean('Exported')->default(0);
            $table->boolean('OpenDoorFlag')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkinout');
    }
};
