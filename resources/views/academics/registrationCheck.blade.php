@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Academics Queries',
    'activePage' => 'registrationCheck',
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
                    <form action="{{ route('academics.registrationCheck') }}" method="GET">
                        @csrf
                        
                        <div class="form-group">
                            <label for="search">Search Students</label>
                            <input type="number" name="student-number" class="form-control" id="student-number" placeholder="Enter student name or ID">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            @if(!empty($results))
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-6 ml-6">  
                                                <div class="card">
                                                    <div class="card-body">                                         
                                                        <h4>Name : {{$results->first()->FirstName}} {{$results->first()->Surname}}</h4>
                                                        <h4>Students Number : {{$results->first()->StudentID}}</h4>
                                                        <h4>Programme : {{$results->first()->ProgrammeName}}</h4>
                                                        <h4>Programme : {{$results->first()->School}}</h4>    
                                                    </div> 
                                                </div>                                       
                                            </div>
                                            <div class="col-md-6 ml-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead class="text-primary">
                                                                    <tr>
                                                                        <th>COURSE</th>
                                                                        <th>COURSE DESCRIPTION</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $previousCourseName = null; // Initialize previous course name variable
                                                                    @endphp
                                                                    @foreach($results as $result)
                                                                        @if($result->CourseName !== $previousCourseName)
                                                                            <tr>
                                                                                <td>{{ $result->CourseName }}</td>
                                                                                <td>{{ $result->CourseDescription }}</td>
                                                                            </tr>
                                                                        @endif
                                                                        @php
                                                                            $previousCourseName = $result->CourseName; // Update the previous course name
                                                                        @endphp
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
@endsection