<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SageInvoice extends Model
{
    use HasFactory;

    protected $connection = 'sage_database'; // Database connection name
    protected $table = 'InvNum'; // Table name
}
