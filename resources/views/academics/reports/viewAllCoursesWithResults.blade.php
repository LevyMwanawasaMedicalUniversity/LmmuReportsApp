@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Academics Queries',
    'activePage' => 'academics',
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
            <h4 class="card-title">All Courses With Results</h4>
          </div>
          <div class="toolbar">
              
           
          
            <div class="row align-items-center">
                <div class="col-md-5 ml-3">
                    
                </div>
                <div class="col-md-6">
                    @if(!empty($results))
                    <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('exportAllCoursesWithResults') }}">Export Data</a>
                    @endif
                </div>
            </div>
        
        </div>
            @if(!empty($results))
            
            <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Programme Code</th>
                            <th>Programme</th>
                            <th>School</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Results Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{$result->CourseCode}}</td>
                            <td>{{$result->CourseName}}</td>
                            <td>{{$result->ProgramName}}</td>
                            <td>{{$result->Programme}}</td>
                            <td>{{$result->School}}</td>
                            <td>{{$result->FirstName}}</td>
                            <td>{{$result->Surname}}</td>
                            <td>{{$result->UnpublishedResults}}</td>                            
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $results->links('pagination::bootstrap-4') }}
            </div>
            
            @else
            <div class="card-body text-center">
                <h3>Please Select An Option To Generate A Report.</h3>
            </div>
            @endif
        </div>
      </div>      
    </div>
  </div>

@endsection