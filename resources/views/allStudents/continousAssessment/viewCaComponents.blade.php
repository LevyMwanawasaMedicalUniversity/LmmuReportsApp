@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Continous Assessment',
    'activePage' => 'docket-index',
    'activeNav' => '',
])

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Continous Assessment Components</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>CA Component</th>
                            <th>Course Code</th>
                            <th>Marks</th> 
                            <th>Total Marks</th>
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
                                    <td>{{$courseCode}}</td>
                                    <td>{{$result->total_marks}}</td>
                                    <td>{{$totalMarks}}</td>
                                    <td class="text-end">
                                        <a href="{{ route('docket.viewInSpecificCaComponent', ['courseId' => $result->course_id, 'caType' => $result->ca_type]) }}" class="btn btn-success">View</a> 
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