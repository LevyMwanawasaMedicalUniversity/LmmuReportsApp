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
        Schema::table('student_study_link_s_r_s', function (Blueprint $table) {
            $table->unsignedBigInteger('ssl_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_study_link_s_r_s', function (Blueprint $table) {
            //
        });
    }
};
