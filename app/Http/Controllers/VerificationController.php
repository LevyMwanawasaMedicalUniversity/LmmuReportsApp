<?php

namespace App\Http\Controllers;

use App\Models\SageClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    public function verifyStudent($studentNumber) {
        $results = $this->checkIfStudentIsRegistered($studentNumber)->get()
            ->values(); 
    
        if ($results->isEmpty()) {
            $results = $this->getRegistrationsFromSisReportsBasedOnReturningAndNewlyAdmittedStudentsSingleStudent($studentNumber)->get()
                ->values();
        }

        if($results->isEmpty()){
            $studentsPayments = SageClient::select('DCLink', 'Account', 'Name',
                DB::raw('SUM(CASE WHEN pa.Description LIKE \'%reversal%\' THEN 0 WHEN pa.Description LIKE \'%FT%\' THEN 0 WHEN pa.Description LIKE \'%DE%\' THEN 0 WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0 ELSE pa.Credit END) AS TotalPayments'),
                DB::raw('SUM(pa.Credit) as TotalCredit'),
                DB::raw('SUM(pa.Debit) as TotalDebit'),
                DB::raw('SUM(pa.Debit) - SUM(pa.Credit) as TotalBalance'),
                DB::raw('SUM(CASE 
                    WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                    WHEN pa.Description LIKE \'%FT%\' THEN 0
                    WHEN pa.Description LIKE \'%DE%\' THEN 0  
                    WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                    WHEN pa.TxDate < \'2024-01-01\' THEN 0 
                    ELSE pa.Credit 
                    END) AS TotalPayment2024'),
                DB::raw('SUM(CASE 
                    WHEN pa.Description LIKE \'%FT%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Debit 
                    WHEN pa.Description LIKE \'%DE%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Debit 
                    ELSE 0 
                    END) - SUM(CASE 
                    WHEN pa.Description LIKE \'%FT%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Credit 
                    WHEN pa.Description LIKE \'%DE%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Credit 
                    ELSE 0 
                    END) AS TrueInvoiceTill2023'),
                DB::raw('
                    (SUM(CASE 
                        WHEN pa.Description LIKE \'%FT%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Debit 
                        WHEN pa.Description LIKE \'%DE%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Debit 
                        ELSE 0 
                    END) - SUM(CASE 
                        WHEN pa.Description LIKE \'%FT%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Credit 
                        WHEN pa.Description LIKE \'%DE%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Credit 
                        ELSE 0 
                    END)) - SUM(CASE 
                        WHEN pa.Description LIKE \'%reversal%\' THEN 0 
                        WHEN pa.Description LIKE \'%FT%\' THEN 0 
                        WHEN pa.Description LIKE \'%DE%\' THEN 0 
                        WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0 
                        ELSE pa.Credit 
                    END) AS BalanceFrom2023
                ')
            )
            ->where('Account', $studentNumber)
            ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
            ->groupBy('DCLink', 'Account', 'Name')
            ->first();

            $balanceFrom2023 = $studentsPayments->BalanceFrom2023;
            
            if($balanceFrom2023 <= 0){
                $results = $this->getSupplemetaryDetails($studentNumber);
            }
        }
    
        return response()->json(['data' =>  $results]);
    }
}
