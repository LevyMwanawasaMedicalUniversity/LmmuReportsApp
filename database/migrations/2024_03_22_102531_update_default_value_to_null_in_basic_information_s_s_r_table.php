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
        Schema::table('basic_information_s_r_s', function (Blueprint $table) {
            $table->string('FirstName')->default(null)->change();
            $table->string('MiddleName')->default(null)->change();
            $table->string('Surname')->default(null)->change();
            $table->string('Sex')->default(null)->change();
            $table->string('GovernmentID')->default(null)->change();
            $table->string('DateOfBirth')->default(null)->change();
            $table->string('PlaceOfBirth')->default(null)->change();
            $table->string('Nationality')->default(null)->change();
            $table->string('StreetName')->default(null)->change();
            $table->string('PostalCode')->default(null)->change();
            $table->string('Town')->default(null)->change();
            $table->string('Country')->default(null)->change();
            $table->string('HomePhone')->default(null)->change();
            $table->string('MobilePhone')->default(null)->change();
            $table->string('Disability')->default(null)->change();
            $table->string('DisabilityType')->default(null)->change();
            $table->string('PrivateEmail')->default(null)->change();
            $table->string('MaritalStatus')->default(null)->change();
            $table->string('StudyType')->default(null)->change();
            $table->string('Status')->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basic_information_s_r_s', function (Blueprint $table) {
            //
        });
    }
};
