@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Docket',
    'activePage' => 'docket-index',
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
                    @if($courseName)
                    <h4 class="card-title">{{ $courseName }} Students </h4>
                    @else
                    <h4 class="card-title">STUDENTS WITH CA : <span style="color: blue;"><b>{{$count}}</b></span> STUDENTS</h4>
                    @endif
                    <div class="col-md-12">
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

                        @if (session('warning'))
                            <div class="alert alert-warning">
                                {{ session('warning') }}
                            </div>
                        @endif
                    </div>
                    @if(!$courseName)
                    
                        <form action="{{ route('students.index') }}" method="GET">
                            @csrf
                            
                            <div class="form-group">
                                <label for="search">Search Students</label>
                                <input type="number" name="student-number" class="form-control" id="student-number" placeholder="Enter student name or ID">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>

                        <form action="{{ route('docket.sendEmailNotice') }}" method="GET">
                            @csrf                           
                            
                            
                            <button type="submit" class="btn btn-info">Send Dockets Notification</button>
                        </form>
                        
                        {{-- <div class="">
                            <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('docket.exportAppealStudents') }}">Export Appeal List</a>
                            
                        </div> --}}
                    
                    @endif
                    @if($courseName)
                    <div class="col-md-6">                        
                        <a class="btn btn-success mt-3 mr-2" href="{{ route('courses.exportListExamList',$courseId) }}">Export List</a>                      
                    </div>
                    @endif
                    {{-- <div class="col-md-6"> 
                        <button class="btn btn-success mt-3 mr-2" id="exportBtn">Export to Excel</button>
                    </div> --}}
                </div>
                <!-- <form action="{{ route('viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="academicYear" style="font-weight: bold; font-size: 16px;">Academic Year</label>
                                <select name="academicYear" class="form-control" required>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="term" style="font-weight: bold; font-size: 16px;">Term</label>
                                <select name="term" class="form-control" required>
                                    <option value="Term-2">Term-2</option>
                                    <option value="Term-1">Term-1</option>                                    
                                    <option value="Term-3">Term-3</option>
                                    <option value="Term-4">Term-4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-5 ml-3">
                            <button class="btn btn-primary -mt3" id="viewResultsBtn" type="submit">
                                View Results
                            </button>
                        </div>
                        <div class="col-md-6">
                        
                        </div>
                    </div>
                </form> -->
                @if(!empty($results))
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead class="text-primary">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Surname</th>
                                <th>Student Number</th>
                                <th>Email</th>
                                <th>Programme</th>
                                <th>School</th>
                                <th>Study Mode</th>
                                <th>Year Of Study</th>
                                <th>Registration Status</th>
                                <th class="text-end">Action</th>
                                <!-- Add other table headers for additional properties -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$result->FirstName}}</td>
                                <td>{{$result->Surname}}</td>
                                <td>{{$result->StudentID}}</td>
                                <td>{{$result->PrivateEmail}}</td>
                                <td>{{$result->Name}}</td>
                                <td>{{$result->Description}}</td>
                                <td>{{$result->StudyType}}</td>
                                <td>{{ $result->Year ? 'Year ' . $result->Year : 'NOT FOUND' }}</td>
                                <td>{{$result->RegistrationStatus}}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                        <a href="{{route('docket.resetStudent',$result->StudentID)}}" class="btn btn-info">Reset</a>
                                        <a href="{{route('students.showStudent',$result->StudentID)}}" class="btn btn-primary">View</a>
                                    </div>
                                </td>
                                <!-- Add other table cells for additional properties -->
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
<script>
    document.getElementById('exportBtn').addEventListener('click', function() {
        var table = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
        XLSX.writeFile(wb, "ALL STUDENTS.xlsx");
    });
</script>

@endsection
