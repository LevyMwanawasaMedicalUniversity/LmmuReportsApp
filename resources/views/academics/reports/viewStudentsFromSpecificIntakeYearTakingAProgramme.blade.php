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
                <form action="{{ route('viewStudentsFromSpecificIntakeYearTakingAProgramme') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="intakeName"><h6><b>Select Intake:</b></h6></label>
                                <select name="intakeName" id="intakeName" class="form-control">
                                    <option value="">Select Intake</option>
                                    <option value="190" @if (isset($intakeName) && $intakeName == '190') selected @endif>190 (2019)</option>
                                    <option value="200" @if (isset($intakeName) && $intakeName == '200') selected @endif>200 (2020)</option>
                                    <option value="210" @if (isset($intakeName) && $intakeName == '210') selected @endif>210 (2021)</option>
                                    <option value="220" @if (isset($intakeName) && $intakeName == '220') selected @endif>220 (2022)</option>
                                    <option value="230" @if (isset($intakeName) && $intakeName == '230') selected @endif>230 (2023)</option>
                                    <option value="240" @if (isset($intakeName) && $intakeName == '240') selected @endif>240 (2024)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="schoolName"><h6><b>Select School:</b></h6></label>
                                <select name="schoolName" id="schoolName" class="form-control" disabled>
                                    <option value="">NONE</option>
                                    <option value="SOHS" {{ $schoolName == 'SOHS' ? 'selected' : '' }}>School of Health Sciences</option>
                                    <option value="SOMCS" {{ $schoolName == 'SOMCS' ? 'selected' : '' }}>School of Medicine and Clinical Sciences</option>
                                    <option value="SOPHES" {{ $schoolName == 'SOPHES' ? 'selected' : '' }}>School of Public Health and Environmental Sciences</option>
                                    <option value="IBBS" {{ $schoolName == 'IBBS' ? 'selected' : '' }}>Institute of Basic and Biomedical Sciences</option>
                                    <option value="SON" {{ $schoolName == 'SON' ? 'selected' : '' }}>School Of Nursing</option>
                                    <option value="DRPGS" {{ $schoolName == 'DRPGS' ? 'selected' : '' }}>Directorate Of Research And Graduate Studies</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 ml-3">
                            <div class="form-group">
                                <label for="programmeName"><h6><b>Select Programme:</b></h6></label>
                                <select name="programmeName" id="programmeName" class="form-control" disabled>
                                    <option value="">NONE</option>
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
                            <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('exportStudentsFromSpecificIntakeYearTakingAProgramme',['intakeName' => $intakeName, 'programmeName' => $programmeName]) }}">Export Data</a>
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
                    {{ $results->appends(['intakeName' => $intakeName, 'programmeName' => $programmeName, 'schoolName' => $schoolName])->links('pagination::bootstrap-4') }}
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
$('#intakeName, #schoolName, #programmeName').on('change', function() {
        var intake = $('#intakeName').val();
        var school = $('#schoolName').val();
        var programme = $('#programmeName').val();

        
    });


    $(document).ready(function() {
    $('#intakeName').on('change', function() {
        var intakeName = $(this).val();

        if (intakeName) {
            $('#schoolName').prop('disabled', false);
        } else {
            $('#schoolName').prop('disabled', true);
            $('#programmeName').prop('disabled', true);
            $('#programmeName').empty();
        }
    });

    $('#schoolName').on('change', function() {
        var schoolName = $(this).val();

        if (schoolName) {
            $('#programmeName').prop('disabled', false);
            getProgrammesBySchool(schoolName);
        } else {
            $('#programmeName').prop('disabled', true);
            $('#programmeName').empty();
        }
    });

    $('#programmeName').on('change', function() {
        var programmeName = $(this).val();

        if (programmeName) {
            $('#viewResultsBtn').prop('disabled', false);            
        } else {
            $('#viewResultsBtn').prop('disabled', true);            
        }
    });

    

    

    function getProgrammesBySchool(schoolName) {
        $.ajax({
            url: "{{ route('getProgrammesBySchoolDynamicForm') }}",
            type: 'GET',
            data: { schoolName: schoolName },
            dataType: 'json',
            success: function(data) {
                $('#programmeName').empty();
                console.log(data);
                $('#programmeName').append('<option value="">--Select Programme--</option>');
                $.each(data, function(key, value) {
                    $('#programmeName').append('<option value="' + value.ShortName + '">' + value.Name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }
});
</script>
