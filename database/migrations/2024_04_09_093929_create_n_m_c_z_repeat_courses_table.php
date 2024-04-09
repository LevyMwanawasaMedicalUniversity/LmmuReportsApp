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
        Schema::create('n_m_c_z_repeat_courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code');
            $table->string('academic_year');
            $table->string('studnent_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('n_m_c_z_repeat_courses');
    }
};
