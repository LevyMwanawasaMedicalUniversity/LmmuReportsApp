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
        Schema::create('sis_reports_sage_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('InvNumber');
            $table->string('Description');
            $table->date('InvDate');
            $table->float('Amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sis_reports_sage_invoices');
    }
};
