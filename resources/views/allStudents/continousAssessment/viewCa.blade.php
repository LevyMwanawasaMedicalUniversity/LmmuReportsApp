@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Continous Assessment',
    'activePage' => 'docket-index',
    'activeNav' => '',
])

@section('content')
{{-- <style>
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
</style> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Continous Assessment</h4>
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
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <p class="text-white">Click the button to view CA details.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>Course </th>
                            <th>Overall CA <span class="badge bg-secondary">40</span></th> 
                            <th class="text-end">Actions</th>   
                        </tr>
                        </thead>
                        <tbody> 
                            @foreach($results as $result)
                            @php
                                $course = App\Models\EduroleCourses::where('ID', $result->course_id)->first();
                                $courseName = $course->CourseDescription;
                                $courseCode = $course->Name;                              

                            @endphp
                            

                                @if(in_array($result->course_id, [1106, 1105]) || $result->study_id != 165)
                                    <tr>
                                        
                                        <td>{{$courseName}}-{{$courseCode}}</td>
                                        {{-- <td>{{$result->total_marks}}</td> --}}
                                        <td>
                                            <span class="badge bg-primary">{{$result->total_marks}}</span> <b>/</b>
                                            <span class="badge bg-secondary">40 </span>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('docket.viewCaComponents', encrypt($result->course_id)) }}" method="GET">
                                                <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                <button type="submit" class="btn btn-success pulsate">CLICK HERE</button>
                                            </form>
                                        </td>

                                    </tr>
                                @else
                                    <tr>
                                        <td>{{$courseName}}-{{$courseCode}}</td>
                                        {{-- <td>{{$result->total_marks}}</td> --}}

                                        
                                        @php
                                            $numberOfUniqueInstances = App\Models\LMMAXStudentsContinousAssessment::where('students_continous_assessments.course_id', $result->course_id)
                                                ->where('students_continous_assessments.delivery_mode', $result->delivery_mode)
                                                ->where('students_continous_assessments.study_id', $result->study_id)
                                                ->whereNotNull('students_continous_assessments.component_id')
                                                ->distinct('students_continous_assessments.component_id')
                                                ->count('students_continous_assessments.component_id');
                                        @endphp
                                        <td>
                                            @if ($numberOfUniqueInstances > 0)
                                                <span class="badge bg-primary">{{ number_format($result->total_marks / $numberOfUniqueInstances, 2) }}</span> <b>/</b>
                                                <span class="badge bg-secondary">40</span>
                                            @else
                                                <span class="badge bg-danger">No components available</span>
                                            @endif
                                        </td>


                                        
                                        <td class="text-end">
                                            <form action="{{ route('docket.viewCaComponentsWithComponent', encrypt($result->course_id)) }}" method="GET">
                                                <input type="hidden" name="study_id" value="{{encrypt($result->study_id)}}">
                                                <input type="hidden" name="delivery_mode" value="{{encrypt($result->delivery_mode)}}">
                                                <input type="hidden" name="course_id" value="{{encrypt($result->course_id)}}">
                                                <button type="submit" class="btn btn-success pulsate">CLICK HERE</button>
                                            </form>
                                    </tr>

                                @endif
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