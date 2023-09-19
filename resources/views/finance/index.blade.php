@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Finance Queries',
    'activePage' => 'finance',
    'activeNav' => '',
])

@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Finance Queries</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                      <tr>
                        <th>Report Name</th>
                        <th>Query Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><a href="{{ route('viewSumOfAllTransactionsOfEachStudent') }}">Sum Of All Transactions Of Each Student</a></td>
                        <td>A report that generates the sum of all the payments made by all students </td>
                      </tr>
                      <tr>
                        <td><a href="#">Total Payments Made By Students in Specific PROGRAMME from a Specific intake Year</a></td>
                        <td>A report that generates the sum of all the payments made by students from a specific intake year. For example, the sum of payments made by each student in DipCMSG from the 2019 intake (190 student numbers)</td>
                      </tr>
                      <tr>
                        <td><a href="#">Total Payments Made By Students in Specific SCHOOL from a Specific intake Year</a></td>
                        <td>A report that generates the sum of all the payments made by students from a specific intake year. For example, the sum of payments made by each student in SOMCS from the 2019 intake (190 student numbers)</td>
                      </tr>
                    </tbody>
                  </table>
            </div>
          </div>
        </div>
      </div>      
    </div>
  </div>

@endsection
