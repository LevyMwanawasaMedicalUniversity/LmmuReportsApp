<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleRoleAssignments extends Model
{
    use HasFactory;

    protected $connection = 'moodle_database';
    protected $table = 'mdl_role_assignments';
    public $timestamps = false;

    protected $fillable = [
        'userid',
        'roleid',
        'contextid',
        'timemodified',
        'timecreated',
    ];
}
