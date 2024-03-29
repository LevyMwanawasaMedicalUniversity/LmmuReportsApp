<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolsSR extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'school_id'
    ];  
}
