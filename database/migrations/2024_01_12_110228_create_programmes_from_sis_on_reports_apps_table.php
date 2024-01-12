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
        Schema::create('programmes_from_sis_on_reports_apps', function (Blueprint $table) {
            $table->id();
            $table->string('programme_id');
            $table->string('programme_code');
            $table->string('programme_name');
            $table->string('programme_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmes_from_sis_on_reports_apps');
    }
};
