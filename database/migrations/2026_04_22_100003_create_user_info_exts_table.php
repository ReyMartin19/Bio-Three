<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_info_exts', function (Blueprint $table) {
            $table->string('userid', 20)->primary()->comment('FK to UserInfo');
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('type_id')->references('type_id')->on('employment_type_exts')->nullOnDelete();
            $table->foreign('unit_id')->references('unit_id')->on('unit_exts')->nullOnDelete();
            $table->foreign('position_id')->references('position_id')->on('position_exts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_info_exts');
    }
};
