<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicInformationSR extends Model
{
    use HasFactory;

    protected $fillable = [
        'FirstName',
        'MiddleName',
        'Surname',
        'Sex',
        'StudentID',
        'GovernmentID',
        'DateOfBirth',
        'PlaceOfBirth',
        'Nationality',
        'StreetName',
        'PostalCode',
        'Town',
        'Country',
        'HomePhone',
        'MobilePhone',
        'Disability',
        'DisabilityType',
        'PrivateEmail',
        'MaritalStatus',
        'StudyType',
        'Status'
    ];
}
