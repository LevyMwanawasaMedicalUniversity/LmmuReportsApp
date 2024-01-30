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
        Schema::create('grades_publisheds', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->date('userdate');
            $table->time('usertime');
            $table->string('StudentNo');
            $table->string('AcademicYear');
            $table->string('Semester');
            $table->string('ProgramNo');
            $table->string('CourseNo');
            $table->string('CAMarks');
            $table->string('ExamMarks');
            $table->string('TotalMarks');
            $table->string('Grade');
            $table->double('Points');
            $table->string('Comment');
            $table->string('KeySet');
            $table->string('Published');
            $table->integer('PeriodID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades_publisheds');
    }
};
