<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holiday_exts', function (Blueprint $table) {
            $table->integer('holidayid')->primary()->comment('FK to Holiday');
            $table->enum('holiday_type', ['Local', 'National', 'Suspension']);
            $table->boolean('is_work_suspended')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_exts');
    }
};
