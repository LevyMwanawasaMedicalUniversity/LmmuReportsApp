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
        Schema::create('basic_information_s_r_s', function (Blueprint $table) {
            $table->id();
            $table->string('FirstName')->default('');
            $table->string('MiddleName')->default('');
            $table->string('Surname')->default('');
            $table->string('Sex')->default('');
            $table->unsignedBigInteger('StudentID');
            $table->string('GovernmentID')->default('');
            $table->string('DateOfBirth')->default('');
            $table->string('PlaceOfBirth')->default('');
            $table->string('Nationality')->default('');
            $table->string('StreetName')->default('');
            $table->string('PostalCode')->default('');
            $table->string('Town')->default('');
            $table->string('Country')->default('');
            $table->string('HomePhone')->default('');
            $table->string('MobilePhone')->default('');
            $table->string('Disability')->default('');
            $table->string('DisabilityType')->default('');
            $table->string('PrivateEmail')->default('');
            $table->string('MaritalStatus')->default('');
            $table->string('StudyType')->default('');
            $table->string('Status')->default('');        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_information_s_r_s');
    }
};
