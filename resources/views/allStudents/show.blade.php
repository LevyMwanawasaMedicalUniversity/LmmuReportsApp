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
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">PAYMENT INFORMATION</h4>
                            <P>Note that your Registration is based on your payments made in 2024</P>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Account</th>
                                        <th>Latest Invoice</th>
                                        {{-- <th>Total Payments Made</th> --}}
                                        <th>Total Payments made in 2024</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $studentId }}</td>
                                        <td>@isset($studentsPayments->LatestInvoiceDate) {{ $studentsPayments->LatestInvoiceDate }} @endisset</td>
                                        {{-- <td>@isset($studentsPayments->TotalPayments) K{{ $studentsPayments->TotalPayments }} @endisset</td> --}}
                                        <td id="payments2024">{{ $studentsPayments->TotalPayment2024 ?? 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            @if($failed == 1)
                                <h4 class="card-title">REPEAT COURSES</h4>
                            @else
                            <h4 class="card-title">YOUR COURSES FOR REGISTRATION</h4>
                            @endif
                        </div>
                        <div class="card-body">  
                            @foreach($currentStudentsCourses->groupBy('CodeRegisteredUnder') as $programme => $courses)
                            <div class="accordion" id="coursesAccordion{{$loop->index}}{{ $studentId }}"> <!-- Concatenate 2024 with loop index -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{$loop->index}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}{{ $studentId }}" aria-expanded="false" aria-controls="collapse{{$loop->index}}{{ $studentId }}">
                                            {{ $programme }}
                                            @php
                                                App\Models\SisReportsSageInvoices;
                                                $sisInvoices = SisReportsSageInvoices::query()->where('InvoiceDescription','=',$programme)->first();
                                                $course = $courses->first();
                                                $amount = $sisInvoices->InvoiceAmount;
                                                $otherFee = 0;
                                                if (strpos($studentId, '190') === 0) {
                                                    $otherFee = 2950;
                                                } else {
                                                    $otherFee = 2625;
                                                }
                                                if($failed == 1){
                                                    $tuitionFee = ($amount -$otherFee) / $theNumberOfCourses;
                                                    $numberOfRepeatCourses = $currentStudentsCourses->count();
                                                    $amount = ($tuitionFee * $numberOfRepeatCourses) + $otherFee;
                                                }else{
                                                    $amount = $amount;
                                                }
                                                
                                            @endphp
                                            <span class="ms-auto registrationFeeRepeat" id="registrationFeeRepeat{{$loop->index}}{{ $studentId }}">Registration Fee = K{{ number_format($amount * 0.25, 2) }}</span>
                                            <span class="ms-auto">Total Invoice = K{{ number_format($amount,2) }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{$loop->index}}{{ $studentId }}" class="accordion-collapse collapse" aria-labelledby="heading{{$loop->index}}" data-bs-parent="#coursesAccordion{{$loop->index}}{{ $studentId }}">
                                        <div class="accordion-body">
                                            {{-- <form method="post" action=""> --}}
                                                @csrf
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
                                                    @foreach($courses as $course)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="courseRepeat[]" value="{{$course->CourseCode}}" class="courseRepeat" id="courseRepeat{{$loop->parent->index}}{{ $studentId }}{{$loop->index}}" checked>
                                                            </td>
                                                            <td>{{$course->CourseCode}}</td>
                                                            <td class="text-end">{{$course->CourseName}}</td>
                                                            @php
                                                                
                                                                $amount = 0;
                                                                $otherFee = 0;
                                                                if (strpos($studentId, '190') === 0) {
                                                                    $otherFee = 2950;
                                                                } else {
                                                                    $otherFee = 2625;
                                                                }
                                                                if($failed == 1){
                                                                    $tuitionFee = $course->InvoiceAmount - $otherFee;
                                                                    $numberOfCourses = $course->numberOfCourses;
                                                                    $amount = ($tuitionFee * $numberOfCourses) + $otherFee;
                                                                }else{
                                                                    $amount = $course->InvoiceAmount;
                                                                }
                                                                
                                                            @endphp
                                                            <td class="text-end">{{$course->Programme}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                <button type="submit" class="btn btn-primary registerButtonRepeat" id="registerButtonRepeat{{$loop->index}}{{ $studentId }}">Register</button>
                                            {{-- </form> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>                
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">ALL COURSES</h4>
                        </div>
                        <div class="card-body"> 
                            @foreach($allCourses->groupBy('CodeRegisteredUnder') as $programme => $courses)                       
                            <div class="accordion" id="coursesAccordion{{$loop->index}}">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{$loop->index}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}" aria-expanded="false" aria-controls="collapse{{$loop->index}}">
                                            {{ $programme }}
                                            @php
                                                $course = $courses->first();
                                            @endphp
                                            <span class="ms-auto registrationFee" id="registrationFee{{$loop->index}}">Registration Fee = K{{ number_format($course->InvoiceAmount * 0.25, 2) }}</span>
                                            <span class="ms-auto">Total Invoice = K{{ number_format($course->InvoiceAmount,2) }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{$loop->index}}" class="accordion-collapse collapse" aria-labelledby="heading{{$loop->index}}" data-bs-parent="#coursesAccordion{{$loop->index}}">
                                        <div class="accordion-body">
                                            {{-- <form method="post" action=""> --}}
                                                @csrf
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
                                                    @foreach($courses as $course)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="course[]" value="{{$course->CourseCode}}" class="course" id="course{{$loop->parent->index}}{{$loop->index}}" checked>
                                                            </td>
                                                            <td>{{$course->CourseCode}}</td>
                                                            <td class="text-end">{{$course->CourseName}}</td>
                                                            <td class="text-end">{{$course->Programme}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                <button type="submit" class="btn btn-primary registerButton" id="registerButton{{$loop->index}}">Register</button>
                                            {{-- </form> --}}
                                        </div>
                                    </div>                                
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
<!-- Modal for eligible registration -->
<div class="modal" id="eligibleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">The following are the courses you have selected for reigtration</h5>
            </div>
            <div class="modal-body">
                <p>You are eligible to register. Do you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ route('sumbitRegistration.student') }}">
                    @csrf
                    <input type="hidden" name="courses" id="coursesInput">
                    <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for ineligible registration -->
<div class="modal" id="ineligibleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Not Eligible for Registration</h5>
            </div>
            <div class="modal-body">
                <p>You do not have enough for registration.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.registerButton').click(function(e) {
        e.preventDefault();

        var index = $(this).attr('id').replace('registerButton', '');
        var registrationFeeText = $('#registrationFee' + index).text();
        var registrationFee = parseFloat(registrationFeeText.replace(/[^0-9\.]/g, ''));
        var payments2024 = parseFloat($('#payments2024').text().replace('K', ''));
    // Store the courses in a variable
        var courses = [];
        $('input[id^="course' + index + '"]:checked').each(function() {
            courses.push($(this).val());
        });
        console.log(courses);
        console.log(registrationFee);
        console.log(payments2024);

    // Show the modal
        if (registrationFee <= payments2024) {
            $('#eligibleModal').modal('show');

      // Populate the modal with the courses
        var courseList = '';
        for (var i = 0; i < courses.length; i++) {
                courseList += '<p>' + courses[i] + '</p>';
        }
        $('#eligibleModal .modal-body').html(courseList);
        $('#coursesInput').val(courses.join(','));
        } else {
            $('#ineligibleModal').modal('show');
        }
    });
});

$(document).ready(function() {
    $('.registerButtonRepeat').click(function(e) {
        e.preventDefault();

        var index = $(this).attr('id').replace('registerButtonRepeat', '');
        var registrationFeeText = $('#registrationFeeRepeat' + index).text();
        var registrationFee = parseFloat(registrationFeeText.replace(/[^0-9\.]/g, ''));
        var payments2024 = parseFloat($('#payments2024').text().replace('K', ''));
    // Store the courses in a variable
        var coursesRepeat = [];
        $('input[id^="courseRepeat' + index + '"]:checked').each(function() {
            coursesRepeat.push($(this).val());
        });
        console.log(coursesRepeat);
        console.log(registrationFee);
        console.log(payments2024);

    // Show the modal
        if (registrationFee <= payments2024) {
            $('#eligibleModal').modal('show');

      // Populate the modal with the courses
        var courseList = '';
        for (var i = 0; i < coursesRepeat.length; i++) {
                courseList += '<p>' + coursesRepeat[i] + '</p>';
        }
        $('#eligibleModal .modal-body').html(courseList);
        $('#coursesInput').val(courses.join(','));
        } else {
            $('#ineligibleModal').modal('show');
        }
    });
});
</script>



@endsection
