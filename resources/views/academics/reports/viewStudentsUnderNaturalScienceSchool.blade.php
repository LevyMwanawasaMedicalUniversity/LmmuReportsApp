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
                    <h4 class="card-title">Academics Queries</h4>
                </div>
                <form action="{{ route('viewStudentsUnderNaturalScienceSchool') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="academicYear"><h6><b>Select Academic Year:</b></h6></label>
                                <select name="academicYear" id="academicYear" class="form-control">                                    
                                    <option value="2024" @if (isset($academicYear) && $academicYear == '2024') selected @endif>2024</option>
                                    <option value="2023" @if (isset($academicYear) && $academicYear == '2023') selected @endif>2023</option>
                                    <option value="2022" @if (isset($academicYear) && $academicYear == '2022') selected @endif>2022</option>
                                    <option value="2021" @if (isset($academicYear) && $academicYear == '2021') selected @endif>2021</option>                                    
                                    <option value="2019" @if (isset($academicYear) && $academicYear == '2019') selected @endif>2019</option>                                   
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="courseCode"><h6><b>Select Course:</b></h6></label>
                                <select name="courseCode" id="courseCode" class="form-control">
                                    <option value="">ALL COURSES</option>
                                    <option value="MAT101" {{ $courseCode == 'MAT101' ? 'selected' : '' }}>MAT101</option>
                                    <option value="PHY101" {{ $courseCode == 'PHY101' ? 'selected' : '' }}>PHY101</option>
                                    <option value="CHM101" {{ $courseCode == 'CHM101' ? 'selected' : '' }}>CHM101</option>
                                    <option value="BIO101" {{ $courseCode == 'BIO101' ? 'selected' : '' }}>BIO101</option>
                                </select>
                            </div>
                        </div>  
                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="courseCode" class="font-weight-bold h6">Number Of Results:</label>
                                <h1 class="pl-3">
                                    @if (isset($results))
                                        {{ $results->total()}}
                                    @else 
                                        0
                                    @endif
                                </h1>
                            </div>
                        </div>                      
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-5 ml-3">
                            <button class="btn btn-primary -mt3" id="viewResultsBtn" type="submit">
                                View Results
                            </button>
                        </div>
                        
                    </div>
                </form>
                <div class="col-md-6">
                            @if(!empty($results))
                                <form method="POST" action="{{ route('exportStudentsUnderNaturalScienceSchool') }}">
                                    @csrf
                                    <input type="hidden" name="academicYear" value="{{ $academicYear }}">
                                    <input type="hidden" name="courseCode" value="{{ $courseCode }}">
                                    <button type="submit" class="btn btn-success float-right mt-3 mr-2">Export Data</button>
                                </form>
                            @endif
                    </div>
                @if(!empty($results))
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                            <tr>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Surname</th>
                                <th>Gender</th>
                                <th>Student Number</th>
                                <th>NRC</th>
                                <th>Programme</th>
                                <th>School</th>
                                <th>Study Mode</th>
                                <!-- Add other table headers for additional properties -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                            <tr>
                                <td>{{$result->FirstName}}</td>
                                <td>{{$result->MiddleName}}</td>
                                <td>{{$result->Surname}}</td>
                                <td>{{$result->Sex}}</td>
                                <td>{{$result->ID}}</td>
                                <td>{{$result->GovernmentID}}</td>
                                <td>{{$result->ProgrammeName}}</td>
                                <td>{{$result->SchoolName}}</td>
                                <td>{{$result->StudyType}}</td>
                                <!-- Add other table cells for additional properties -->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    {{ $results->appends(['academicYear' => $academicYear, 'courseCode' => $courseCode])->links('pagination::bootstrap-4') }}
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

