<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_exts', function (Blueprint $table) {
            $table->id('unit_id');
            $table->integer('dept_id')->index()->comment('FK to Depts');
            $table->string('unit_name', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_exts');
    }
};
