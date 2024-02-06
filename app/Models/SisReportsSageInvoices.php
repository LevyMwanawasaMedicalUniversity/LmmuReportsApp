<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisReportsSageInvoices extends Model
{
    use HasFactory;

    protected $fillable = [
        'InvNumber',
        'Description',    
        'InvDate', 
        'Amount',
        'AutoIndex'   
    ];
}
