<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SageClient extends Model
{
    use HasFactory;
    protected $connection = 'sage_database'; // Database connection name
    protected $table = 'Client'; // Table name

    // Your SageClient model properties and methods

    // public function basicInformation()
    // {
    //     return $this->hasOne(BasicInformation::class, 'ID', 'Account');
    // }

    // public function sagePostAR()
    // {
    //     return $this->hasMany(SagePostAR::class, 'AccountLink', 'DCLink');
    // }

   

    // public function getResults()
    // {
    //     return $this->select('DCLink', 'Account', 'Name')
    //         ->selectRaw('SUM(CASE WHEN pa.Description NOT LIKE "%reversal%" THEN pa.Credit ELSE 0 END) AS TotalPayment')
    //         ->selectRaw('CONVERT(VARCHAR, lid.LatestTxDate, 23) AS LatestInvoiceDate')
    //         ->join('PostAR AS pa', 'pa.AccountLink', '=', 'DCLink')
    //         ->leftJoinSub(SagePostAr::latestInvoiceDates(), 'lid', function ($join) {
    //             $join->on('pa.AccountLink', '=', 'lid.AccountLink');
    //         })
    //         ->groupBy('DCLink', 'Account', 'Name')
    //         ->groupByRaw('CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN "Invoiced" ELSE "Not Invoiced" END')
    //         ->get();
    // }

    
}
