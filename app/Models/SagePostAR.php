<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SagePostAR extends Model
{
    use HasFactory;
    protected $connection = 'sage_database'; // Database connection name
    protected $table = 'PostAR'; // Table name
    

    // Your SagePostAR model properties and methods

    public function sageClient()
    {
        return $this->belongsTo(SageClient::class, 'DCLink', 'AccountLink');
    }
}
