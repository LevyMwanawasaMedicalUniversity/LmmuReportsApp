@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Course Registration',
    'activePage' => 'docket-index',
    'activeNav' => '',
])

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<div class="panel-header panel-header-sm"></div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title no-print">Course Registration</h4>
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">ALL COURSES</h4>
                        </div>
                        <div class="card-body">
                            @foreach($groupedCoursesAll as $programme => $courses)
                                <form method="POST" action="{{ auth()->user()->hasAnyRole(['Administrator', 'Developer']) ? route('sumbitRegistration.student') : route('student.submitCourseRegistration') }}" onsubmit="return prepareCourses(this)">
                                    @csrf
                                    <div class="accordion" id="coursesAccordion{{ $loop->index }}">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                                                    {{ $programme }}
                                                </button>
                                            </h2>
                                            <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                                            <input type="hidden" name="courses" value="" class="courses-input">
                                            <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#coursesAccordion{{ $loop->index }}">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead class="text-primary">
                                                                <tr>
                                                                    <th>Select</th>
                                                                    <th>Course Code</th>
                                                                    <th class="text-end">Course Name</th>
                                                                    <th class="text-end">Program</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($courses->unique('CourseCode') as $course)
                                                                    @if(is_object($course) || is_array($course))
                                                                        <tr>
                                                                            <td>
                                                                                <input type="checkbox" name="course[]" value="{{ $course['CourseCode'] }}" class="course-checkbox" checked>
                                                                            </td>
                                                                            <td>{{ $course['CourseCode'] }}</td>
                                                                            <td class="text-end">{{ $course['CourseName'] }}</td>
                                                                            <td class="text-end">{{ $course['Programme'] }}</td>
                                                                        </tr>
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="4">Invalid course data.</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary" id="registerButton{{ $loop->index }}">Register</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('allStudents.components.registrationModals')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function prepareCourses(form) {
        let selectedCourses = [];
        $(form).find('.course-checkbox:checked').each(function() {
            selectedCourses.push($(this).val());
        });
        $(form).find('.courses-input').val(selectedCourses.join(','));
        return true;
    }
</script>
@endsection
