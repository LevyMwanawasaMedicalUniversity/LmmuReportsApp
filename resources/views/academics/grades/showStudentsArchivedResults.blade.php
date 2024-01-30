@extends('layouts.app', [
    'namePage' => 'Archive Results',
    'class' => 'sidebar-mini ',
    'activePage' => 'viewGradesArchive',
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
                    <h4 class="card-title no-print">2023 RESULTS for {{$studentNumber}}</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Course Code</th>
                                <th>Continous Assessment</th>
                                <th>Exam Marks</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                            <tr>
                                <td>{{$result->ProgramNo}}</td>
                                <td>{{$result->CourseNo}}</td>
                                <td>{{$result->CAMarks}}</td>
                                <td>{{$result->ExamMarks}}</td>
                                <td>{{$result->Grade}}</td>
                            </tr>
                            @endforeach
                        </tbody>

                </div>
            </div>
        </div>
    </div>
  </div>
  @endsection