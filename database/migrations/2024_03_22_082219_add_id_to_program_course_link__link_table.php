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
        Schema::table('program_course_links_s_r_s', function (Blueprint $table) {
            $table->unsignedBigInteger('pcl_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_course_links_s_r_s', function (Blueprint $table) {
            //
        });
    }
};
