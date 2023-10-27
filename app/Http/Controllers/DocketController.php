<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;

class DocketController extends Controller
{
    public function index(){
        return view('docket.index');
    }

    public function import(){
        return view('docket.import');
    }

    public function uploadStudents(Request $request)
    {
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'term' => 'required',
        ]);

        // Get the academic year and term from the form
        $academicYear = $request->input('academicYear');
        $term = $request->input('term');

        // Process the uploaded file
        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $isHeaderRow = true; // Flag to identify the header row

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }

                    // Assuming the student number is in the first column (index 1)
                    $studentNumber = $row->getCellAtIndex(0)->getValue();

                    // Check if the student number already exists within the same academic year and term
                    $isDuplicate = Student::where('student_number', $studentNumber)
                        ->where('academic_year', $academicYear)
                        ->where('term', $term)
                        ->exists();

                    if (!$isDuplicate) {
                        // Insert a new student record into the database
                        Student::create([
                            'student_number' => $studentNumber,
                            'academic_year' => $academicYear,
                            'term' => $term,
                        ]);
                    }
                }
            }

            $reader->close();

            // Provide a success message
            return redirect()->back()->with('success', 'Students imported successfully.');
        }

        // Handle errors or validation failures
        return redirect()->back()->with('error', 'Failed to upload students.');
    }
}
