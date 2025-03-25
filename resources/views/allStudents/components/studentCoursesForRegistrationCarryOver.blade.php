<div class="card">
    <div class="card-header">
        @if($failed == 1)
            <h4 class="card-title">
                @if(isset($carryOverCoursesCount) && $carryOverCoursesCount <= 2)
                    REPEAT COURSES AND CURRENT YEAR COURSES
                @else
                    REPEAT COURSES
                @endif
            </h4>
        @else
            <h4 class="card-title">YOUR COURSES FOR REGISTRATION</h4>
        @endif
    </div>
    <div class="card-body">  
        @php
            $registrationFeesRepeat = [];
            $totalFeesRepeat = [];
            $allCoursesForRegistration = collect();
            
            // Combine all courses into a single collection
            foreach($currentStudentsCourses->groupBy('CodeRegisteredUnder') as $programme => $programCourses) {
                foreach($programCourses as $course) {
                    $course->ProgramName = $programme;
                    $allCoursesForRegistration->push($course);
                }
            }
            
            // Calculate total fees
            $totalAmount = 0;
            $totalRegistrationFee = 0;
            
            foreach($currentStudentsCourses->groupBy('CodeRegisteredUnder') as $programme => $courses) {
                $sisInvoices = \App\Models\SageInvoice::where('Description','=',$programme)->first();
                $amount = $sisInvoices ? $sisInvoices->InvTotExclDEx : 0;
                $otherFee = 2625;

                if($failed == 1){
                    $tuitionFee = ($amount - $otherFee) / $theNumberOfCourses;
                    $numberOfRepeatCourses = $courses->count();
                    $amount = ($tuitionFee * $numberOfRepeatCourses) + $otherFee;
                }
                
                $totalAmount += $amount;
                $totalRegistrationFee += round($amount * 0.25, 2);
            }
        @endphp
        
        <div class="accordion" id="coursesAccordionCombined{{ $studentId }}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCombined">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCombined{{ $studentId }}" aria-expanded="false" aria-controls="collapseCombined{{ $studentId }}">
                        <span class="ms-auto">All Courses for Registration</span>
                        <span class="ms-auto registrationFeeRepeat" id="registrationFeeRepeatCombined{{ $studentId }}">Registration Fee = K{{ number_format($totalRegistrationFee, 2) }}</span>
                        <span class="ms-auto totalFeeRepeat" id="totalFeeRepeatCombined{{ $studentId }}">Total Invoice = K{{ number_format($totalAmount, 0) }}</span>
                    </button>
                </h2>
                <div id="collapseCombined{{ $studentId }}" class="accordion-collapse collapse" aria-labelledby="headingCombined" data-bs-parent="#coursesAccordionCombined{{ $studentId }}">
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
                                            @if(isset($carryOverCoursesCount) && $carryOverCoursesCount <= 2)
                                            <th class="text-end">Course Type</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allCoursesForRegistration as $index => $course)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="courseRepeat[]" value="{{$course->CourseCode}}" class="courseRepeat" id="courseRepeatCombined{{ $studentId }}{{$index}}" checked>
                                            </td>
                                            <td>{{$course->CourseCode}}</td>
                                            <td class="text-end">{{$course->CourseName}}</td>
                                            <td class="text-end">{{$course->ProgramName}}</td>
                                            @if(isset($carryOverCoursesCount) && $carryOverCoursesCount <= 2)
                                            <td class="text-end">
                                                @php
                                                    $isCarryOver = false;
                                                    if (isset($carryOverCourses)) {
                                                        foreach ($carryOverCourses as $carryOverCourse) {
                                                            if (isset($carryOverCourse['Course']) && $carryOverCourse['Course'] == $course->CourseCode) {
                                                                $isCarryOver = true;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if($isCarryOver)
                                                    <span class="badge bg-warning">Repeat</span>
                                                @else
                                                    <span class="badge bg-primary">Current</span>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                            
                            {{-- Blade Conditional Logic to show modals based on balance and payments --}}
                            @php
                                $isEligible = ($totalRegistrationFee <= $studentsPayments->TotalPayment2024) && ($actualBalance <= 0);
                                $shortfall = $totalRegistrationFee - $studentsPayments->TotalPayment2024;
                            @endphp

                            {{-- Show eligibility modal --}}
                            @if($isEligible || ($failed == 1) || (auth()->user()->hasAnyRole(['Administrator', 'Developer'])) )
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eligibleModalRepeatCombined">Register</button>
                                <!-- Eligible Modal -->
                                <div class="modal fade" id="eligibleModalRepeatCombined" tabindex="-1" aria-labelledby="eligibleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Eligible To Register</h5>
                                            </div>
                                            <div class="modal-body">
                                                <p>You are submitting the following courses for registration:</p>
                                                <ul>
                                                    @foreach($allCoursesForRegistration as $course)
                                                        <li>
                                                            {{ $course->CourseCode }} - {{ $course->CourseName }}
                                                            @if(isset($carryOverCoursesCount) && $carryOverCoursesCount <= 2)
                                                                @php
                                                                    $isCarryOver = false;
                                                                    if (isset($carryOverCourses)) {
                                                                        foreach ($carryOverCourses as $carryOverCourse) {
                                                                            if (isset($carryOverCourse['Course']) && $carryOverCourse['Course'] == $course->CourseCode) {
                                                                                $isCarryOver = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                                @if($isCarryOver)
                                                                    <span class="badge bg-warning">Repeat</span>
                                                                @else
                                                                    <span class="badge bg-primary">Current</span>
                                                                @endif
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <p>Total Invoice: K {{ number_format($totalAmount, 2) }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <!-- Form for submitting courses -->
                                                <form method="POST" action="{{ auth()->user()->hasAnyRole(['Administrator', 'Developer']) ? route('sumbitRegistration.student') : route('student.submitCourseRegistration') }}">
                                                    @csrf
                                                    <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                                                    
                                                    <!-- Pass the selected courses as a hidden input -->
                                                    @foreach($allCoursesForRegistration as $course)
                                                        <input type="hidden" name="courses[]" value="{{ $course->CourseCode }}">
                                                    @endforeach
                                                    
                                                    <button type="submit" class="btn btn-success">Yes</button>
                                                </form>
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($totalRegistrationFee > $studentsPayments->TotalPayment2024)
                                {{-- Show ineligible modal for insufficient registration fee --}}
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ineligibleModalRepeatCombined">Register</button>
                                <!-- Ineligible Modal for Shortfall -->
                                <div class="modal fade" id="ineligibleModalRepeatCombined" tabindex="-1" aria-labelledby="ineligibleModalLabel" aria-hidden="true">
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
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#balanceModalRepeatCombined">Register</button>
                                <!-- Ineligible Modal for Outstanding Balance -->
                                <div class="modal fade" id="balanceModalRepeatCombined" tabindex="-1" aria-labelledby="balanceModalLabel" aria-hidden="true">
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
            </div>
        </div>
    </div>
</div>
