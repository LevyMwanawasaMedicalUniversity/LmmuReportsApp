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
        Schema::create('course_registration', function (Blueprint $table) {
            $table->id();
            $table->string('StudentID');
            $table->string('CourseID');
            // $table->foreign('StudentID')->references('student_number')->on('students');
            // $table->foreignId('CourseID')->constrained('all_courses', 'course_code');
            $table->date('EnrolmentDate');
            $table->integer('Approved')->default(0);
            $table->integer('PeriodID')->default(0);
            $table->year('Year');
            $table->integer('Semester');
            $table->integer('Moodle')->default(0);
            $table->integer('LateRegistration')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_registration');
    }
};
