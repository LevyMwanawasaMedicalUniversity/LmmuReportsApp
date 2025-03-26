@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Course Registration',
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
                    @include('allStudents.components.finacialInformationWithCarryOver')
                    @include('allStudents.components.studentCoursesForRegistrationCarryOver')                                                                    
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">ALL COURSES</h4>
                        </div>
                        <div class="card-body"> 
                            @php
                                $registrationFees = [];
                                $totalFees = [];
                            @endphp
                            @foreach($allCourses->groupBy('CodeRegisteredUnder') as $programme => $courses)                       
                            <div class="accordion" id="coursesAccordion{{$loop->index}}">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{$loop->index}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}" aria-expanded="false" aria-controls="collapse{{$loop->index}}">
                                            {{ $programme }}
                                            @php
                                                $sisInvoices = \App\Models\SageInvoice::where('Description','=',$programme)->first();
                                                $course = $courses->first();
                                                $amount = $sisInvoices ? $sisInvoices->InvTotExclDEx : 0;

                                                $index = $loop->index;
                                                $registrationFees[$index] = round($amount * 0.25, 2);
                                                $totalFees[$index] = round($amount, 2);
                                            @endphp
                                    @isset($sisInvoices)
                                            <span class="ms-auto registrationFee" id="registrationFee{{$loop->index}}">Registration Fee = K{{ number_format($amount * 0.25, 2) }}</span>
                                            <span class="ms-auto totalFee" id="totalFee{{$loop->index}}">Total Invoice = K{{ number_format($amount,0) }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{$loop->index}}" class="accordion-collapse collapse" aria-labelledby="heading{{$loop->index}}" data-bs-parent="#coursesAccordion{{$loop->index}}">
                                        <div class="accordion-body">
                                            <form method="POST" action="{{ auth()->user()->hasAnyRole(['Administrator', 'Developer']) ? route('sumbitRegistration.student') : route('student.submitCourseRegistration') }}">
                                                @csrf
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead class="text-primary">
                                                            <tr>
                                                                <th>Select</th>
                                                                <th>Course Code</th>
                                                                <th>Course Name</th>
                                                                <th>Semester</th>
                                                                <th>Course Type</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($courses->where('CodeRegisteredUnder', $programme)->where('CourseModeID','!=', 6) as $course)
                                                            <tr>
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input courseRepeat" type="checkbox" name="courses[]" value="{{ $course->CourseID }}" id="course{{$loop->parent->index}}{{$loop->index}}">
                                                                    </div>
                                                                </td>                                    
                                                                <td>{{ $course->CourseID }}</td>
                                                                <td>{{ $course->Title }}</td>
                                                                <td>{{ $course->Semester }}</td>
                                                                <td>
                                                                    <span class="badge {{ $course->isCarryOver ? 'bg-warning' : 'bg-primary' }}">{{ $course->isCarryOver ? 'Repeat' : 'Current' }}</span>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <input type="hidden" name="studentId" value="{{ $studentId }}">
                                                @php
                                                    // For registration, student balance information will be checked via JavaScript
                                                    $registrationFee = $registrationFees[$index] ?? 0;
                                                @endphp
                                                <button type="submit" class="btn btn-primary registerButton" id="registerButton{{$loop->index}}">Register</button>
                                            </form>
                                        </div>
                                    </div>
                                    @endisset                              
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div> 
                </div>               
            </div>
        </div>
    </div>
</div>
@endsection
