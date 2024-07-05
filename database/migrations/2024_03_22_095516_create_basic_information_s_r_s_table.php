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
            $table->string('FirstName')->nullable();
            $table->string('MiddleName')->nullable();
            $table->string('Surname')->nullable();
            $table->string('Sex')->nullable();
            $table->unsignedBigInteger('StudentID')->nullable(); // Assuming you want this nullable as well
            $table->string('GovernmentID')->nullable();
            $table->string('DateOfBirth')->nullable(); // Already nullable
            $table->string('PlaceOfBirth')->nullable();
            $table->string('Nationality')->nullable();
            $table->string('StreetName')->nullable();
            $table->string('PostalCode')->nullable();
            $table->string('Town')->nullable();
            $table->string('Country')->nullable();
            $table->string('HomePhone')->nullable();
            $table->string('MobilePhone')->nullable();
            $table->string('Disability')->nullable();
            $table->string('DisabilityType')->nullable();
            $table->string('PrivateEmail')->nullable();
            $table->string('MaritalStatus')->nullable();
            $table->string('StudyType')->nullable();
            $table->string('Status')->nullable();
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
