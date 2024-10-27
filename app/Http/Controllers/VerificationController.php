<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verifyStudent($studentNumber) {
        $results = $this->checkIfStudentIsRegistered($studentNumber)->get()
            ->values(); 
    
        if ($results->isEmpty()) {
            $results = $this->getRegistrationsFromSisReportsBasedOnReturningAndNewlyAdmittedStudentsSingleStudent($studentNumber)->get()
                ->values();
        }

    
        return response()->json(['data' =>  $results]);
    }
}
