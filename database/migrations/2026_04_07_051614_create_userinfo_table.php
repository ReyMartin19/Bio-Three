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
        Schema::create('userinfo', function (Blueprint $table) {
            $table->string('Userid', 50)->primary();
            $table->string('UserCode', 50)->nullable();
            $table->string('Name', 100)->nullable();
            $table->string('Sex', 10)->nullable();
            $table->string('Pwd', 100)->nullable();
            $table->integer('Deptid')->nullable();
            $table->string('Nation', 50)->nullable();
            $table->date('Birthday')->nullable();
            $table->date('EmployDate')->nullable();
            $table->string('Telephone', 50)->nullable();
            $table->string('Duty', 100)->nullable();
            $table->string('NativePlace', 100)->nullable();
            $table->string('IDCard', 50)->nullable();
            $table->string('Address', 255)->nullable();
            $table->string('Mobile', 50)->nullable();
            $table->string('Educated', 50)->nullable();
            $table->string('Polity', 50)->nullable();
            $table->string('Specialty', 50)->nullable();
            $table->boolean('IsAtt')->default(1);
            $table->boolean('Isovertime')->default(0);
            $table->boolean('Isrest')->default(0);
            $table->string('Remark', 255)->nullable();
            $table->integer('MgFlag')->nullable();
            $table->string('CardNum', 50)->nullable();
            $table->binary('Picture')->nullable();
            $table->integer('UserFlag')->nullable();
            $table->integer('Groupid')->nullable();
            $table->integer('ClassFlag')->nullable();
            $table->string('OtherInfo', 255)->nullable();
            $table->integer('admingroupid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userinfo');
    }
};
