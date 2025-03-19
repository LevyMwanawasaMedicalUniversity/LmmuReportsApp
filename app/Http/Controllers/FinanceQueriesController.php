<?php

namespace App\Http\Controllers;

use App\Models\SisReportsSageInvoices;
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

    public function viewInvoicesPerProgramme(){
        
        $results = $this->getInvoicesPerProgramme()->get();
        // return $results;
        foreach ($results as $result) {
            $descriptionParts = explode('-', $result->Description);
            if(isset($descriptionParts[1])){
                if($descriptionParts[1] == 'FT'){
                    $studyMode = 'Fulltime';
                }else{
                    $studyMode = 'Distance';
                }
            }
            SisReportsSageInvoices::updateOrCreate(
                [ 'AutoIndex' => $result->AutoIndex ],
                [
                    'InvNumber' => $result->InvNumber,
                    'InvoiceDescription' => $result->Description,
                    'InvDate' => $result->InvDate, 
                    'InvoiceAmount' => $result->InvTotExcl,
                    'InvoiceProgrammeCode' => $descriptionParts[0] ?? 'none',
                    'InvoiceModeOfStudy' => $studyMode ?? 'none',
                    'InvoiceYearOfInvoice' =>  $descriptionParts[2] ?? 'none',
                    'InvoiceYearOfStudy' => $descriptionParts[3] ?? 'none',
                ]
            );
        }    

        $results = SisReportsSageInvoices::paginate(20);
        
        return view('finance.viewInvoicesPerProgramme',compact('results'));
    }

    public function exportAllProgrammeInvoices(){
        
        $headers = [
            'InvNumber',
            'Description',
            'InvDate',
            'Amount',
        ];
        
        $rowData = [
            'InvNumber',
            'Description',
            'InvDate',
            'InvTotExcl'
        ];
        
        $results = $this->getInvoicesPerProgramme()->get();
        $filename = 'InvoicesPerProgramme';
        
        return $this->exportDataFromArray($headers, $rowData, $results, $filename);
    }


    public function exportAllPaymentInformation(){
        set_time_limit(1200000);
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
            '2024 Invoice',
            'Year Of Study',
            'Total Payments',
            'Total Payments Before 2023',
            'Total Payments 2022',
            'Total Payments 2023',
            'Total Payments 2024',
            'Total Payments 2025',
            '2023 Invoice Status',
            '2024 Invoice Status',
            'Latest Invoice Date',
            'Total Credit',
            'Total Debit',
            'Total Balance'
            
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
            'ShortName',
            'StudyName',
            'School',
            'StudyType',
            'RegistrationStatus',
            'InvoiceAmount',
            'YearOfStudy',
            'TotalPayments',
            'TotalPaymentBefore2023',
            'TotalPayment2022',
            'TotalPayment2023',
            'TotalPayment2024',
            'TotalPayment2025',
            '2023InvoiceStatus',
            '2024InvoiceStatus',
            'LatestInvoiceDate',
            'TotalCredit',
            'TotalDebit',
            'TotalBalance'
        ];
        
        $results = $this->getSumOfAllTransactionsOfEachStudent();        
        $filename = 'SumOfAllTransactionsOfEachStudent';
        
        return $this->exportDataFromArray($headers, $rowData, $results, $filename);
    }
}
