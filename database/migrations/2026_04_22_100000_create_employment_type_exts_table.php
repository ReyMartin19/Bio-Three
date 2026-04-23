<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_type_exts', function (Blueprint $table) {
            $table->id('type_id');
            $table->string('type_name', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_type_exts');
    }
};
