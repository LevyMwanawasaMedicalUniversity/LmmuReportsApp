<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramSR extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_id',
        'program_name'
    ];
}
