<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_arrangement_exts', function (Blueprint $table) {
            $table->id('arrangement_id');
            $table->string('userid', 20)->index();
            $table->enum('arrangement_type', ['Full Flexi', 'Fixed Flexi', 'WFH']);
            $table->integer('schclassid')->nullable()->comment('FK to Schedule if any');
            $table->date('covered_period_start')->nullable();
            $table->date('covered_period_end')->nullable();
            $table->string('preferred_wfh_days', 100)->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Denied'])->default('Pending');
            $table->string('recommended_by', 100)->nullable();
            $table->string('approved_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_arrangement_exts');
    }
};
