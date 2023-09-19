<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicInformation extends Model
{
    use HasFactory;

    protected $connection = 'edurole_database';
    protected $table = 'basic-information';

    // Your BasicInformation model properties and methods

    public function sageClient()
    {
        return $this->belongsTo(SageClient::class, 'Account', 'ID');
    }

    public function sagePostAR()
    {
        return $this->hasMany(SagePostAR::class, 'DCLink', 'Account');
    }
}
