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
        Schema::create('dept', function (Blueprint $table) {
            $table->integer('Deptid')->autoIncrement();
            $table->string('DeptName', 50)->unique();
            $table->integer('SupDeptid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dept');
    }
};
