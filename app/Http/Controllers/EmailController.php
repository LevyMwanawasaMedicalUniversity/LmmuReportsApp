<?php

namespace App\Http\Controllers;

use App\Mail\SendAnEmail;
use App\Models\BasicInformation;
use App\Models\Courses;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
// use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailController extends Controller
{
    public function sendTestEmail($studentID) {
        $studentResults = $this->getStudentResults($studentID);
        $courses = $this->getStudentCourses($studentID);
    
        // Define the PDF file name (e.g., using the studentID)
        $fileName = $studentID . '.pdf';
    
        // Generate the PDF and save it to the "public" disk
        $pdf = PDF::loadView('emails.pdf', compact('studentResults', 'courses'));
        $pdfPath = storage_path('app/' . $fileName);
        $pdf->save($pdfPath);

        $privateEmail = BasicInformation::find($studentID);

        $privateEmail->PrivateEmail;
    
        // Send the email with the PDF attachment
        Mail::to($privateEmail)->send(new SendAnEmail($pdfPath));
    
        // Send the email with the PDF attachment
        // Mail::to('azwel.simwinga@lmmu.ac.zm',
        //         'serah.mbewe@lmmu.ac.zm',
        //     'kabwenda.moonga@lmmu.ac.zm',)->send(new SendAnEmail($pdfPath));
    
        // Delete the temporary PDF file after sending the email
        unlink($pdfPath);
    
        return "Test email sent successfully!";
    }


    public function getStudentResults($studentId){
        // try{
            
        // }catch(Exception $e){
            
        // }
        $academicYear= 2023;
        $student = Student::query()
                        ->where('student_number','=', $studentId)
                        ->first();
        if($student){
            $getStudentNumber = $student->student_number;
            $studentNumbers = [$getStudentNumber];
            $studentResults = $this->getAppealStudentDetails($academicYear, $studentNumbers)->first();
        }else{
            return back()->with('error', 'NOT FOUND.');               
        }

        return $studentResults;
    }
        
        
    public function getStudentCourses($studentId){
               
        
        $this->setAndSaveTheCourses($studentId);
        // Retrieve all unique Student values from the Course model
        $courses = Courses::where('Student', $studentId)->get();
        // return $courses;

        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return $courses;
    }
}
