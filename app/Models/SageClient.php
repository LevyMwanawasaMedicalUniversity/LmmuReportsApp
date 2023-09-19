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

    public function basicInformation()
    {
        return $this->hasOne(BasicInformation::class, 'ID', 'Account');
    }

    public function sagePostAR()
    {
        return $this->hasMany(SagePostAR::class, 'AccountLink', 'DCLink');
    }
    
}
