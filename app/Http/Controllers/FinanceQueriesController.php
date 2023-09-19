<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceQueriesController extends Controller
{
    public function index(){
        return view('finance.index');
    }

    public function viewSumOfAllTransactionsOfEachStudent(Request $request){
        $results = $this->getSumOfAllTransactionsOfEachStudent()->paginate('20');

        return view('finance.reports.viewSumOfAllTransactionsOfEachStudent',compact('results'));
    }
}
