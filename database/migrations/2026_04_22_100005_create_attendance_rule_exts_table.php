<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_rule_exts', function (Blueprint $table) {
            $table->string('rule_key', 50)->primary();
            $table->string('rule_value', 50);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_rule_exts');
    }
};
