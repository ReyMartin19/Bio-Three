<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_exts', function (Blueprint $table) {
            $table->id('position_id');
            $table->unsignedBigInteger('unit_id');
            $table->string('position_title', 100);
            $table->timestamps();

            $table->foreign('unit_id')->references('unit_id')->on('unit_exts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_exts');
    }
};
