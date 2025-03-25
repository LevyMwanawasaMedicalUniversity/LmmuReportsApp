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
                    @include('allStudents.components.finacialInformation')
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
                                                                <th class="text-end">Course Name</th>
                                                                <th class="text-end">Program</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($courses as $course)
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" name="courses[]" value="{{$course->CourseCode}}" class="course" id="course{{$loop->parent->index}}{{$loop->index}}" checked>
                                                                </td>
                                                                <td>{{$course->CourseCode}}</td>
                                                                <td class="text-end">{{$course->CourseName}}</td>
                                                                <td class="text-end">{{$course->Programme}}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                                                
                                                {{-- Blade Conditional Logic to show modals based on balance and payments --}}
                                                @php
                                                    $isEligible = ($registrationFees[$index] <= $studentsPayments->TotalPayment2024) && ($actualBalance <= 0);
                                                    $shortfall = $registrationFees[$index] - $studentsPayments->TotalPayment2024;
                                                @endphp

                                                {{-- Show eligibility modal --}}
                                                @if($isEligible || ($failed == 1) || (auth()->user()->hasAnyRole(['Administrator', 'Developer'])) )
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eligibleModalRepeat{{$loop->index}}">Register</button>
                                                    <!-- Eligible Modal -->
                                                    <div class="modal fade" id="eligibleModalRepeat{{$loop->index}}" tabindex="-1" aria-labelledby="eligibleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Eligible To Register</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>You are submitting the following @if($failed == 1)repeat @endif courses for registration:</p>
                                                                    <ul>
                                                                        @foreach($courses as $course)
                                                                            <li>{{ $course->CourseCode }} - {{ $course->CourseName }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                    <p>Total Invoice: K {{ number_format($totalFees[$index], 2) }}</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <!-- Form for submitting courses -->
                                                                    <form method="POST" action="{{ auth()->user()->hasAnyRole(['Administrator', 'Developer']) ? route('sumbitRegistration.student') : route('student.submitCourseRegistration') }}">
                                                                        @csrf
                                                                        <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                                                                        
                                                                        <!-- Pass the selected courses as a hidden input -->
                                                                        @foreach($courses as $course)
                                                                            <input type="hidden" name="courses[]" value="{{ $course->CourseCode }}">
                                                                        @endforeach
                                                                        
                                                                        <button type="submit" class="btn btn-success">Yes</button>
                                                                    </form>
                                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($registrationFees[$index] > $studentsPayments->TotalPayment2024)
                                                    {{-- Show ineligible modal for insufficient registration fee --}}
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ineligibleModal{{$loop->index}}">Register</button>
                                                    <!-- Ineligible Modal for Shortfall -->
                                                    <div class="modal fade" id="ineligibleModal{{$loop->index}}" tabindex="-1" aria-labelledby="ineligibleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Not Eligible for Registration</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p style="color:red;">You are short of the registration fee by: K {{ number_format($shortfall, 2) }}</p>
                                                                    <p>Kindly make a payment to proceed with the registration.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($actualBalance > 0)
                                                    {{-- Show ineligible modal for outstanding balance --}}
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#balanceModal{{$loop->index}}">Register</button>
                                                    <!-- Ineligible Modal for Outstanding Balance -->
                                                    <div class="modal fade" id="balanceModal{{$loop->index}}" tabindex="-1" aria-labelledby="balanceModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Outstanding Balance</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p style="color:red;">You currently have a balance on your account of: K {{ number_format($actualBalance, 2) }}</p>
                                                                    <p>Please clear your balance to proceed with registration.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
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
