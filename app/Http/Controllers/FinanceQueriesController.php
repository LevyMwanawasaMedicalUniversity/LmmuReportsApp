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
}
