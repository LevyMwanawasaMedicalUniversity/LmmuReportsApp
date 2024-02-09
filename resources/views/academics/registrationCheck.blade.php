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
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif
                </div>
                <div class="card-body"> 
                    @if($results instanceof Illuminate\Support\Collection)                    
                        @if(count($results) > 0)
                            <div class="card">
                                <div class="card-header" style="text-align: center;">
                                    <b><h2 class="card-title" style="color: green; text-decoration-color: green;">REGISTERED STUDENT</h2></b>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 ml-6">  
                                            <div class="card">
                                                <div class="card-body">                                         
                                                    <h4>Name : {{$results->first()->FirstName}} {{$results->first()->Surname}}</h4>
                                                    <h4>Students Number : {{$results->first()->StudentID}}</h4>
                                                    <h4>Programme : {{$results->first()->ProgrammeName}}</h4>
                                                    <h4>School : {{$results->first()->School}}</h4>   
                                                    <h4>Year Of Study : {{$results->first()->YearOfStudy}}</h4> 
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
                                                                    $previousCourseName = null;
                                                                @endphp
                                                                @foreach($results as $result)
                                                                    @if($result->CourseName !== $previousCourseName)
                                                                        <tr>
                                                                            <td>{{ $result->CourseName }}</td>
                                                                            <td>{{ $result->CourseDescription }}</td>
                                                                        </tr>
                                                                    @endif
                                                                    @php
                                                                        $previousCourseName = $result->CourseName; 
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
                        @elseif (count($results) == 0)
                            <div class="card">
                                <div class="card-header" style="text-align: center;">
                                    <b><h2 class="card-title" style="color: red; text-decoration-color: red;">STUDENT NOT REGISTERED</h2></b>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 ml-6">  
                                            <div class="card">
                                                <div class="card-body"> 
                                                    <h4>Student not registered for 2024 Academic Year</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 ml-6">  
                                            <div class="card">
                                                <div class="card-body"> 
                                                    <h4>Student not registered</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                       
                        @endif 
                    @else
                        <div class="card">
                            <div class="card-header" style="text-align: center;">
                                <b><h2 class="card-title" style="color: blue; text-decoration-color: blue;">PLEASE SEARCH FOR A STUDENT</h2></b>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 ml-6">  
                                        <div class="card">
                                            <div class="card-body"> 
                                                <h4>Enter Student Number in the search bar</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ml-6">  
                                        <div class="card">
                                            <div class="card-body"> 
                                                <h4>Enter Student Number in the search bar</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif                       
                </div>
            </div>
        </div>
    </div>
</div>
@endsection