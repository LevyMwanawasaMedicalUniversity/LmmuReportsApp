<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verifyStudent($studentNumber) {
        $results = $this->checkIfStudentIsRegistered($studentNumber)->get()
            ->values(); 
    
        // if ($results->isEmpty()) {

    
        return response()->json(['data' =>  $results]);
    }
}
