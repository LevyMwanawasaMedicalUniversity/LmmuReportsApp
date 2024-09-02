@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Continous Assessment',
    'activePage' => 'docket-index',
    'activeNav' => '',
])

@section('content')
<style>
@keyframes pulsate {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.pulsate {
    animation: pulsate 1s infinite;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Continous Assessment Components for {{$courseName}} - {{$courseCode}}</h4>
                <div class="alert alert-info" role="alert">
                    <p class="text-white">Click the button to view CA Component details.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>Course Component</th>
                            <th>CA OUT OF <span class="badge bg-secondary">40</span></th> 
                            <th class="text-end">Actions</th>   
                        </tr>
                        </thead>
                        <tbody> 
                            @foreach($results as $result)
                                {{-- @php
                                    $course = App\Models\EduroleCourses::where('ID', $result->course_id)->first();
                                    $courseName = $course->CourseDescription;
                                    $courseCode = $course->Name;                              
                                @endphp --}}

                                @php
                                    $marks = App\Models\LMMAXStudentsContinousAssessment::where('students_continous_assessments.course_id', $result->course_id)
                                        ->where('students_continous_assessments.delivery_mode', $result->delivery_mode)
                                        ->where('students_continous_assessments.study_id', $result->study_id)
                                        ->where('students_continous_assessments.component_id', $result->course_component_id)
                                        ->where('students_continous_assessments.student_id', $studentNumber)
                                        // ->whereIn('ca_type', [1,2,3]) 
                                        ->join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
                                        ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'))
                                        ->groupBy('students_continous_assessments.student_id')
                                        ->first();
                                @endphp

                                @if (!$marks)
                                    @continue
                                @endif

                                <tr>
                                    {{-- <td>{{$courseName}}-{{$courseCode}}</td> --}}
                                    <td>{{$result->component_name}}</td>
                                    {{-- <td>{{$result->total_marks}}</td> --}}
                                    <td>
                                        <span class="badge bg-primary">{{ number_format($marks->total_marks, 2) }}</span> <b>/</b>
                                        <span class="badge bg-secondary">40</span>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('docket.viewCaComponents', encrypt($result->course_id)) }}" method="GET">
                                            <input type="hidden" name="study_id" value="{{ encrypt($result->study_id) }}">
                                            <input type="hidden" name="delivery_mode" value="{{ encrypt($result->delivery_mode) }}">
                                            <input type="hidden" name="course_id" value="{{ encrypt($result->course_id) }}">
                                            <input type="hidden" name="course_component_id" value="{{ encrypt($result->course_component_id) }}">
                                            <input type="hidden" name="component_name" value="{{ encrypt($result->component_name) }}">
                                            <button type="submit" class="btn btn-success pulsate">CLICK HERE</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach                
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>      
        </div>
    </div>
@endsection