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
            @php
                $course = App\Models\EduroleCourses::where('ID', $results[0]->course_id)->first();
                $courseName = $course->CourseDescription;
                $courseCode = $course->Name;
            @endphp

            <div class="card-header">
                <h4 class="card-title">Continous Assessment Components for {{$courseName}}-{{$courseCode}}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>CA Component</th>
                            {{-- <th>Course Code</th> --}}
                            <th>Marks / Total Marks <span class="badge bg-secondary">40</span></th>
                            <th class="text-end">Actions</th>   
                        </tr>
                        </thead>
                        <tbody> 
                            @foreach($results as $result)
                            @php
                                $course = App\Models\EduroleCourses::where('ID', $result->course_id)->first();
                                $courseName = $course->CourseDescription;
                                $courseCode = $course->Name;

                                $totalMarks = \App\Models\LMMAXAssessmentTypes::join('c_a_type_marks_allocations', 'c_a_type_marks_allocations.assessment_type_id', '=', 'assessment_types.id')
                                    ->join('students_continous_assessments','students_continous_assessments.ca_type', '=', 'assessment_types.id')
                                    ->where('students_continous_assessments.students_continous_assessment_id', $result->students_continous_assessment_id)
                                    ->where('students_continous_assessments.student_id', $studentNumber)
                                    ->where('students_continous_assessments.ca_type', $result->ca_type)
                                    ->where('c_a_type_marks_allocations.delivery_mode', $result->delivery_mode)
                                    ->where('c_a_type_marks_allocations.study_id', $result->study_id)
                                    ->where('c_a_type_marks_allocations.course_id', $result->course_id)
                                    //->where('students_continous_assessments.students_continous_assessment_id', $result->students_continous_assessment_id)
                                    ->select('c_a_type_marks_allocations.total_marks')
                                    ->first();

                                $totalMarks = $totalMarks->total_marks;

                            @endphp
                                <tr>
                                    
                                    <td>{{$result->assesment_type_name}}</td>
                                    {{-- <td>{{$courseCode}}</td> --}}
                                    {{-- <td>{{$result->total_marks}} / {{$totalMarks}}</td> --}}
                                    <td>
                                        <span class="badge bg-primary">{{$result->total_marks}}</span> <b>/</b> 
                                        <span class="badge bg-secondary">{{$totalMarks}}</span>
                                    </td>
                                    {{-- <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ ($result->total_marks / $totalMarks) * 100 }}%;" aria-valuenow="{{$result->total_marks}}" aria-valuemin="0" aria-valuemax="{{$totalMarks}}">
                                                {{$result->total_marks}} / {{$totalMarks}}
                                            </div>
                                        </div>
                                    </td> --}}
                                    {{-- <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{$result->total_marks}} / {{$totalMarks}}</span>
                                            <div class="progress" style="flex-grow: 1; height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ ($result->total_marks / $totalMarks) * 100 }}%;" aria-valuenow="{{$result->total_marks}}" aria-valuemin="0" aria-valuemax="{{$totalMarks}}">
                                                </div>
                                            </div>
                                        </div>
                                    </td> --}}
                                    <td class="text-end">
                                        <a href="{{ route('docket.viewInSpecificCaComponent', ['courseId' => $result->course_id, 'caType' => $result->ca_type]) }}" class="btn btn-success pulsate">CLICK HERE</a> 
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