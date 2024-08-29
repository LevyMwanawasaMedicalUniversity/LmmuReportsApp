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
                <h4 class="card-title">Continous Assessment</h4>
                <div class="alert alert-info" role="alert">
                    <p class="text-white">Click the button to view CA details.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>Course </th>
                            <th>CA OUT OF <span class="badge bg-secondary">40</span></th> 
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
                                <tr>
                                    
                                    <td>{{$courseName}}-{{$courseCode}}</td>
                                    {{-- <td>{{$result->total_marks}}</td> --}}
                                    <td>
                                        <span class="badge bg-primary">{{$result->total_marks}}</span> <b>/</b>
                                        <span class="badge bg-secondary">40</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{route('docket.viewCaComponents',$result->course_id )}}" class="btn btn-success pulsate">CLICK HERE</a>
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