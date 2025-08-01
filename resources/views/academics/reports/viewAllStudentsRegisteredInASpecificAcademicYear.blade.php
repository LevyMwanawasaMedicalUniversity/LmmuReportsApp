@extends('layouts.app', [
    'class' => 'sidebar-mini',
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
                    <h4 class="card-title">All Students Registered In A Specific Academic Year</h4>
                </div>
                <form action="{{ route('viewAllStudentsRegisteredInASpecificAcademicYear') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="academicYear"><h6><b>Select Academic Year:</b></h6></label>
                                <select name="academicYear" id="academicYear" class="form-control">
                                    <option value="">NONE</option>
                                    <option value="2019" {{ $academicYear == '2019' ? 'selected' : '' }}>2019</option>
                                    <option value="2020" {{ $academicYear == '2020' ? 'selected' : '' }}>2020</option>
                                    <option value="2021" {{ $academicYear == '2021' ? 'selected' : '' }}>2021</option>
                                    <option value="2022" {{ $academicYear == '2022' ? 'selected' : '' }}>2022</option>
                                    <option value="2023" {{ $academicYear == '2023' ? 'selected' : '' }}>2023</option>
                                    <option value="2024" {{ $academicYear == '2024' ? 'selected' : '' }}>2024</option>
                                    <option value="2025" {{ $academicYear == '2025' ? 'selected' : '' }}>2025</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="enrollmentDate"><h6><b>Enrollment Date (From):</b></h6></label>
                                <input type="date" name="enrollmentDate" id="enrollmentDate" class="form-control" value="{{ $enrollmentDate ?? '' }}">
                                <small class="form-text text-muted">Optional: Filter students enrolled after this date</small>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-5 ml-3">
                            <button class="btn btn-primary -mt3" type="submit">
                                View Results
                            </button>
                        </div>
                        <div class="col-md-6">
                            @if(!empty($results))
                            <h4>Total Number Of Results {{ $results->total() }}</h4>
                            <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('exportAllStudentsRegisteredInASpecificAcademicYear',['academicYear' => $academicYear, 'enrollmentDate' => $enrollmentDate ?? '']) }}">Export Data</a>
                            @endif
                        </div>
                    </div>
                </form>
                @if(!empty($results))
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <tr>
                                    <th>First Name</th>
                                    <th>Middle Namee</th>
                                    <th>Surname</th>
                                    <th>Student Number</th>
                                    <th>NRC</th>
                                    <th>Mode Of Study</th>
                                    <th>Programme Name</th>
                                    <th>Year Of Study</th>
                                    <th>School</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                <tr>
                                    <td>{{$result->FirstName}}</td>
                                    <td>{{$result->MiddleName}}</td>
                                    <td>{{$result->Surname}}</td>
                                    <td>{{$result->ID}}</td>
                                    <td>{{$result->GovernmentID}}</td>
                                    <td>{{$result->StudyType}}</td>
                                    <td>{{$result->ProgrammeName}}</td>
                                    <td>{{$result->YearOfStudy}}</td>
                                    <td>{{$result->SchoolName}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $results->appends(['academicYear' => $academicYear])->links('pagination::bootstrap-4') }}
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