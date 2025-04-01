<div class="card">
    <div class="card-header">
        @if($failed == 1)
            <h4 class="card-title">REPEAT COURSES</h4>
        @else
            <h4 class="card-title">YOUR COURSES FOR REGISTRATION</h4>
        @endif
    </div>
    <div class="card-body">  
        @php
            $registrationFeesRepeat = [];
            $totalFeesRepeat = [];
            
            // Determine which year to use based on student ID
            $useYear = '2023';
            if (substr($studentId, 0, 3) === '190') {
                $useYear = '2019';
            }
            
            // First, filter to keep only programs that match the target year
            $filteredPrograms = collect();
            
            // Pre-process the program codes to match target year
            foreach($currentStudentsCourses->groupBy('CodeRegisteredUnder') as $programme => $courses) {
                $programParts = explode('-', $programme);
                $yearMatch = false;
                
                // Check if this program already has the correct year
                if (count($programParts) >= 3 && strpos($programParts[2], $useYear) !== false) {
                    $yearMatch = true;
                }
                
                // If it's already the right year, use it as is
                if ($yearMatch) {
                    $filteredPrograms[$programme] = $courses;
                } else {
                    // Try to convert the program to the target year
                    $modifiedProgramme = $programme;
                    
                    if (count($programParts) >= 3) {
                        // Try to replace year in the program name
                        if (strpos($programParts[2], '2019') !== false) {
                            $programParts[2] = str_replace('2019', $useYear, $programParts[2]);
                            $modifiedProgramme = implode('-', $programParts);
                        } else if (strpos($programParts[2], '2023') !== false) {
                            $programParts[2] = str_replace('2023', $useYear, $programParts[2]);
                            $modifiedProgramme = implode('-', $programParts);
                        }
                    }
                    
                    // Only add if it's different and we can find an invoice for it
                    if ($modifiedProgramme !== $programme) {
                        $invoice = \App\Models\SageInvoice::where('Description', '=', $modifiedProgramme)->first();
                        if ($invoice) {
                            // Use the modified program name but same courses
                            $filteredPrograms[$modifiedProgramme] = $courses;
                        } else {
                            // No match with modified program, keep original as fallback
                            $filteredPrograms[$programme] = $courses;
                        }
                    } else {
                        // No year detected, keep original
                        $filteredPrograms[$programme] = $courses;
                    }
                }
            }
        @endphp
        
        @foreach($filteredPrograms as $programme => $courses)
        <div class="accordion" id="coursesAccordion{{$loop->index}}{{ $studentId }}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{$loop->index}}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}{{ $studentId }}" aria-expanded="false" aria-controls="collapse{{$loop->index}}{{ $studentId }}">
                        <span class="ms-auto">{{ $programme }}</span>
                        @php
                            // Get invoice for this program
                            $sisInvoices = \App\Models\SageInvoice::where('Description', '=', $programme)->first();
                            
                            $course = $courses->first();
                            $amount = $sisInvoices ? $sisInvoices->InvTotExclDEx : 0;
                            $otherFee = 2625;

                            if($failed == 1){
                                $tuitionFee = ($amount - $otherFee) / $theNumberOfCourses;
                                $numberOfRepeatCourses = $courses->count();
                                $amount = ($tuitionFee * $numberOfRepeatCourses) + $otherFee;
                            }
                            $index = $loop->index . $studentId;
                            $registrationFeesRepeat[$index] = round($amount * 0.25, 2);
                            $totalFeesRepeat[$index] = round($amount, 2);
                        @endphp
                @isset($sisInvoices)
                    
                        <span class="ms-auto registrationFeeRepeat" id="registrationFeeRepeat{{$loop->index}}{{ $studentId }}">Registration Fee = K{{ number_format($amount * 0.25, 2) }}</span>
                        <span class="ms-auto totalFeeRepeat" id="totalFeeRepeat{{$loop->index}}{{ $studentId }}">Total Invoice = K{{ number_format($amount,0) }}</span>
                    </button>
                </h2>
                <div id="collapse{{$loop->index}}{{ $studentId }}" class="accordion-collapse collapse" aria-labelledby="heading{{$loop->index}}" data-bs-parent="#coursesAccordion{{$loop->index}}{{ $studentId }}">
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
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                            
                            {{-- Blade Conditional Logic to show modals based on balance and payments --}}
                            @php
                                $isEligible = ($registrationFeesRepeat[$index] <= $studentsPayments->TotalPayment2025) && ($actualBalance <= 0);
                                $shortfall = $registrationFeesRepeat[$index] - $studentsPayments->TotalPayment2025;
                            @endphp

                            {{-- Show eligibility modal --}}
                            @if($isEligible  )
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
                                                <p>Total Invoice: K {{ number_format($totalFeesRepeat[$index], 2) }}</p>
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
                            @elseif($registrationFeesRepeat[$index] > $studentsPayments->TotalPayment2025)
                                {{-- Show ineligible modal for insufficient registration fee --}}
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ineligibleModalRepeat{{$loop->index}}">Register</button>
                                <!-- Ineligible Modal for Shortfall -->
                                <div class="modal fade" id="ineligibleModalRepeat{{$loop->index}}" tabindex="-1" aria-labelledby="ineligibleModalLabel" aria-hidden="true">
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
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#balanceModalRepeat{{$loop->index}}">Register</button>
                                <!-- Ineligible Modal for Outstanding Balance -->
                                <div class="modal fade" id="balanceModalRepeat{{$loop->index}}" tabindex="-1" aria-labelledby="balanceModalLabel" aria-hidden="true">
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
