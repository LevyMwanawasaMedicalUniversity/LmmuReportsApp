@extends('layouts.app', [
    'namePage' => 'Dashboard',
    'class' => 'sidebar-mini',
    'activePage' => 'home',
    'activeNav' => '',
])

@section('content')
<div class="panel-header panel-header-sm">
    <!-- You can add content or elements here if needed -->
</div>
<div class="content">
    <div class="row">
        @if($isStudentRegisteredOnEdurole || $isStudentRegisteredOnSisReports)            
            @foreach($allResults->groupBy('AcademicYear') as $yearOfStudy => $grades)
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title no-print">{{$yearOfStudy}} RESULTS for {{ $studentNumber }}</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Course Code</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grades as $grade)
                                        <tr>
                                            <td>{{$grade->ProgramNo}}</td>
                                            <td>{{$grade->CourseNo}}</td>
                                            <td>{{$grade->Grade}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach            
        @else
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title no-print">2023 RESULTS for {{ $studentNumber }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Course Code</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                    <tr>
                                        <td>{{$result->ProgramNo}}</td>
                                        <td>{{$result->CourseNo}}</td>
                                        <td>{{$result->Grade}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
