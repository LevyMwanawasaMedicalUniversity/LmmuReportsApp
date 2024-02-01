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
                    <h4 class="card-title">Registered Students Per Year In Year Of Study</h4>
                </div>
                <form action="{{ route('viewRegisteredStudentsPerYearInYearOfStudy') }}" method="GET">
                    <div class="row">

                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="yearOfStudy"><h6><b>Select Year Of Study:</b></h6></label>
                                <select name="yearOfStudy" id="yearOfStudy" class="form-control">
                                <option value="">Year Of Study</option>
                                    <option value="y1" @if (isset($yearOfStudy) && $yearOfStudy == 'y1') selected @endif>YEAR 1</option>
                                    <option value="y2" @if (isset($yearOfStudy) && $yearOfStudy == 'y2') selected @endif>YEAR 2</option>
                                    <option value="y3" @if (isset($yearOfStudy) && $yearOfStudy == 'y3') selected @endif>YEAR 3</option>
                                    <option value="y4" @if (isset($yearOfStudy) && $yearOfStudy == 'y4') selected @endif>YEAR 4</option>
                                    <option value="y5" @if (isset($yearOfStudy) && $yearOfStudy == 'y5') selected @endif>YEAR 5</option>
                                    <option value="y6" @if (isset($yearOfStudy) && $yearOfStudy == 'y6') selected @endif>YEAR 6</option>
                                </select>
                            </div>
                        </div>

                         <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="academicYear"><h6><b>ACADEMIC YEAR:</b></h6></label>
                                <select name="academicYear" id="academicYear" class="form-control" disabled>
                                    <option value="">ACADEMIC YEAR</option>
                                    <option value="2019" @if (isset($academicYear) && $academicYear == '2019') selected @endif>2019</option>
                                    <option value="2020" @if (isset($academicYear) && $academicYear == '2020') selected @endif>2020</option>
                                    <option value="2021" @if (isset($academicYear) && $academicYear == '2021') selected @endif>2021</option>
                                    <option value="2022" @if (isset($academicYear) && $academicYear == '2022') selected @endif>2022</option>
                                    <option value="2023" @if (isset($academicYear) && $academicYear == '2023') selected @endif>2023</option>
                                    <option value="2024" @if (isset($academicYear) && $academicYear == '2024') selected @endif>2024</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-5 ml-3">
                            <button class="btn btn-primary -mt3" id="viewResultsBtn" type="submit" disabled>
                                View Results
                            </button>
                        </div>
                        <div class="col-md-6">
                            @if(!empty($results))
                            <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('exportRegisteredStudentsPerYearInYearOfStudy',['yearOfStudy' => $yearOfStudy, 'academicYear' => $academicYear]) }}">Export Data</a>
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
                                <th>Middle Name</th>
                                <th>Surname</th>
                                <th>Gender</th>
                                <th>Student Number</th>
                                <th>NRC</th>
                                <th>Programme</th>
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
                                <td>{{$result->Name}}</td>
                                <td>{{$result->StudyType}}</td>
                                <!-- Add other table cells for additional properties -->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    {{ $results->appends(['yearOfStudy' => $yearOfStudy, 'academicYear' => $academicYear])->links('pagination::bootstrap-4') }}
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$('#yearOfStudy, #academicYear').on('change', function() {
        var yearOfStudy = $('#yearOfStudy').val();
        var academicYear = $('#academicYear').val();

        if (yearOfStudy){
            $('#academicYear').prop('disabled', false);
        }else{
            $('#academicYear').prop('disabled', true);
        }

        if (yearOfStudy && academicYear) {
            $('#viewResultsBtn').prop('disabled', false);
        } else {
            $('#viewResultsBtn').prop('disabled', true);
        }
    });
});
</script>
