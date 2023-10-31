<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SagePostAR extends Model
{
    use HasFactory;
    protected $connection = 'sage_database'; // Database connection name
    protected $table = 'PostAR'; // Table name
    

    // Your SagePostAR model properties and methods

    // public function sageClient()
    // {
    //     return $this->belongsTo(SageClient::class, 'DCLink', 'AccountLink');
    // }

    // public function scopeLatestInvoiceDates($query)
    // {
    //     return $query->select('AccountLink', DB::raw('MAX(TxDate) AS LatestTxDate'))
    //         ->where('Description', 'LIKE', '%-%-%')
    //         ->where('Debit', '>', 0)
    //         ->groupBy('AccountLink');
    // }
}
