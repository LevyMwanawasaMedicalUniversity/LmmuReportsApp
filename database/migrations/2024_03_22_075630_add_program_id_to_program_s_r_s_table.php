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
        Schema::table('program_s_r_s', function (Blueprint $table) {
            $table->unsignedBigInteger('programme_id');
            $table->dropColumn('program_name');
        });

        Schema::table('program_s_r_s', function (Blueprint $table) {
            $table->string('program_name')->after('programme_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_s_r_s', function (Blueprint $table) {
            //
        });
    }
};
