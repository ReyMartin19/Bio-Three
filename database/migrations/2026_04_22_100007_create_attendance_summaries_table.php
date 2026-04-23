<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('userid', 20)->index();
            $table->date('record_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->boolean('is_late')->default(false);
            $table->integer('late_minutes')->default(0);
            $table->boolean('is_undertime')->default(false);
            $table->integer('undertime_minutes')->default(0);
            $table->string('status', 50)->default('Present')->comment('Present, Absent, Leave, Event');
            $table->timestamps();

            $table->unique(['userid', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_summaries');
    }
};
