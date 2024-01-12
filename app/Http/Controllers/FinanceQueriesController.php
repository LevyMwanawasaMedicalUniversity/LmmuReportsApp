<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FinanceQueriesController extends Controller
{
    public function index(){
        return view('finance.index');
    }

    public function viewSumOfAllTransactionsOfEachStudent(Request $request){
        set_time_limit(1200000);
        $results = $this->getSumOfAllTransactionsOfEachStudent();
    
        // Check if results is null
        if ($results === null) {
            $results = [];
        }
    
        // Get current page
        $page = LengthAwarePaginator::resolveCurrentPage();
    
        // Items per page
        $perPage = 20;
    
        // Get current items
        $currentItems = array_slice($results, ($page * $perPage) - $perPage, $perPage);
    
        // Create a paginator
        $results = new LengthAwarePaginator($currentItems, count($results), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        // return $results;
        return view('finance.reports.viewSumOfAllTransactionsOfEachStudent',compact('results'));
    }


    public function exportAllPaymentInformation(){

        $headers = [
            'First Name',
            'Middle Name',
            'Surname',
            'Sex',
            'Student ID',
            'Government ID',
            'Email',
            'Mobile Number',
            'Programme Code',
            'Study Name',
            'School',
            'Study Type',
            'Registration Status',
            'Year Of Study',
            'Total Payments',
            'Total Payments Before 2023',
            'Total Payments 2023',
            'Total Payments 2024',
            '2023 Invoice Status',
            '2024 Invoice Status',
            'Latest Invoice Date',
            
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'Sex',
            'StudentID',
            'GovernmentID',
            'PrivateEmail',
            'MobilePhone',
            'ProgrammeCode',
            'StudyName',
            'School',
            'StudyType',
            'RegistrationStatus',
            'YearOfStudy',
            'TotalPayments',
            'TotalPaymentBefore2023',
            'TotalPayment2023',
            'TotalPayment2024',
            '2023InvoiceStatus',
            '2024InvoiceStatus',
            'LatestInvoiceDate'
        ];
        
        $results = $this->getSumOfAllTransactionsOfEachStudent();        
        $filename = 'SumOfAllTransactionsOfEachStudent';
        
        return $this->exportDataFromArray($headers, $rowData, $results, $filename);
    }
}
