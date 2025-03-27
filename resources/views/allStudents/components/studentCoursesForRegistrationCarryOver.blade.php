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
            $uniqueCourses = collect();
            $seenCourses = [];
            
            // Combine all courses into a single collection and filter out duplicates
            foreach($currentStudentsCourses->groupBy('CodeRegisteredUnder') as $programme => $programCourses) {
                foreach($programCourses as $course) {
                    // Only add the course if we haven't seen this course code before
                    if (!isset($seenCourses[$course->CourseCode])) {
                        $course->ProgramName = $programme;
                        $uniqueCourses->push($course);
                        $seenCourses[$course->CourseCode] = true;
                    }
                }
            }
            
            // Calculate total fees
            $totalAmount = 0;
            $totalRegistrationFee = 0;
            $carryOverAmount = 0;
            $currentYearAmount = 0;
            
            // Debug array to store all values
            $debugValues = [];
            
            // Determine which year version to use based on student ID
            $useYear = '2023';
            if (substr($studentId, 0, 3) === '190') {
                $useYear = '2019';
            }
            $debugValues['target_year'] = $useYear;
            $debugValues['student_id_prefix'] = substr($studentId, 0, 3);
            
            // First, identify which courses are carry-over courses
            $carryOverCourseCodes = collect();
            if (isset($carryOverCourses)) {
                foreach ($carryOverCourses as $carryOverCourse) {
                    if (isset($carryOverCourse['Course'])) {
                        $carryOverCourseCodes->push($carryOverCourse['Course']);
                    }
                }
            }
            
            // Group courses by program for fee calculation
            foreach($currentStudentsCourses->groupBy('CodeRegisteredUnder') as $programme => $courses) {
                // Get the program code which might contain a year
                $programParts = explode('-', $programme);
                $currentYearProgramme = $programme; // Default to current program
                
                // Ensure we're using the correct year version based on student ID
                if (count($programParts) >= 3) {
                    // Check if the program year matches what we want based on student ID
                    $hasCorrectYear = (strpos($programParts[2], $useYear) !== false);
                    
                    // If it doesn't match, create the correct version
                    if (!$hasCorrectYear) {
                        // Store the original for debugging
                        $originalProgramme = $programme;
                        
                        // Create the correct program name with our target year
                        $programParts[2] = $useYear;
                        $currentYearProgramme = implode('-', $programParts);
                        
                        $debugValues['program_replaced'][$programme] = $currentYearProgramme;
                    }
                }
                
                // STRICTLY use the program version that matches the student ID year
                $sisInvoices = \App\Models\SageInvoice::where('Description', '=', $currentYearProgramme)->first();
                
                // Log if we couldn't find the correct version
                if (!$sisInvoices && $currentYearProgramme !== $programme) {
                    $debugValues['missing_invoice'][] = $currentYearProgramme;
                    
                    // Fall back to original only if we absolutely have to
                    $sisInvoices = \App\Models\SageInvoice::where('Description', '=', $programme)->first();
                    if ($sisInvoices) {
                        $debugValues['fallback_to_original'][] = $programme;
                    }
                }
                
                $amount = $sisInvoices ? $sisInvoices->InvTotExclDEx : 0;
                $otherFee = 2625; // Fixed fee component
                
                // Add to debug values
                $debugValues[$programme] = [
                    'invoice_amount' => $amount,
                    'other_fee' => $otherFee,
                    'course_count' => $courses->count(),
                    'carry_over_count' => 0,
                    'current_count' => 0
                ];
                
                // Filter courses to separate carry-over from current year
                $programCarryOverCourses = $courses->filter(function($course) use ($carryOverCourseCodes) {
                    return $carryOverCourseCodes->contains($course->CourseCode);
                });
                
                $programCurrentCourses = $courses->filter(function($course) use ($carryOverCourseCodes) {
                    return !$carryOverCourseCodes->contains($course->CourseCode);
                });
                
                // Update debug counts
                $debugValues[$programme]['carry_over_count'] = $programCarryOverCourses->count();
                $debugValues[$programme]['current_count'] = $programCurrentCourses->count();
                
                // Only process if we have courses for this program
                if ($programCarryOverCourses->count() > 0 || $programCurrentCourses->count() > 0) {
                    // If we have current year courses and <= 2 carry-over courses, use current year invoice
                    if ($programCurrentCourses->count() > 0 && $carryOverCourseCodes->count() <= 2) {
                        // Verify this is a Y2 program (for current courses)
                        if (strpos($currentYearProgramme, '-Y2') !== false) {
                            // Only use this as current year amount if it matches our target year
                            if (strpos($currentYearProgramme, $useYear) !== false) {
                                $currentYearAmount = $amount;
                                $debugValues[$programme]['current_year_fee'] = $currentYearAmount;
                                $debugValues[$programme]['using_program'] = $currentYearProgramme;
                                $debugValues['current_year_program'] = $currentYearProgramme;
                            }
                        }
                    }
                    
                    // For repeating courses, calculate per-course fee
                    if ($programCarryOverCourses->count() > 0) {
                        // Calculate tuition fee per course (total invoice minus other fees, divided by number of courses)
                        $tuitionFeePerCourse = ($amount - $otherFee) / $theNumberOfCourses;
                        $debugValues[$programme]['tuition_per_course'] = $tuitionFeePerCourse;
                        
                        // Add tuition for each carry-over course
                        $programCarryOverFee = $tuitionFeePerCourse * $programCarryOverCourses->count();
                        $carryOverAmount += $programCarryOverFee;
                        
                        $debugValues[$programme]['carry_over_fee'] = $programCarryOverFee;
                    }
                }
            }
            
            // Combine fees - current year amount already includes other fees
            // Make sure we're using the correct current year amount based on student ID
            $totalAmount = $currentYearAmount + $carryOverAmount;
            $totalRegistrationFee = round($totalAmount * 0.25, 2);
            
            // Add final values to debug
            $debugValues['final'] = [
                'carry_over_amount' => $carryOverAmount,
                'current_year_amount' => $currentYearAmount,
                'total_amount' => $totalAmount,
                'registration_fee' => $totalRegistrationFee
            ];
            
            // Debug info
            $debug = [
                'carryOverCourses' => $carryOverCourseCodes->toArray(),
                'carryOverAmount' => $carryOverAmount,
                'currentYearAmount' => $currentYearAmount,
                'totalAmount' => $totalAmount,
                'totalRegistrationFee' => $totalRegistrationFee,
                'debugValues' => $debugValues
            ];


            $studentsBalance = -1 * $actualBalance;
            $isStudentsEligibleToRegister = $studentsBalance - $totalRegistrationFee;
            
            // Display debug values in console
            echo '<script>';
            echo 'console.log(' . json_encode($debugValues) . ');';
            echo '</script>';
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
                                        @foreach($uniqueCourses as $index => $course)
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
                            
                            {{-- Use the new $isStudentsEligibleToRegister variable for conditional logic --}}
                            {{-- For debugging purposes --}}
                            @php
                                // We already have $isStudentsEligibleToRegister calculated earlier
                                // Shortfall calculation for display purposes
                                $shortfall = $isStudentsEligibleToRegister;
                                
                                // Debug info
                                echo '<script>';
                                echo 'console.log("Student Balance: " + ' . json_encode($studentsBalance) . ');';
                                echo 'console.log("Registration Fee: " + ' . json_encode($totalRegistrationFee) . ');';
                                echo 'console.log("Eligibility Value: " + ' . json_encode($isStudentsEligibleToRegister) . ');';
                                echo '</script>';
                            @endphp

                            {{-- Show eligibility modal if $isStudentsEligibleToRegister is >= 0 or for administrators --}}
                            @if($isStudentsEligibleToRegister >= 0  )
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
                                                    @foreach($uniqueCourses as $course)
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
                                                    @foreach($uniqueCourses as $course)
                                                        <input type="hidden" name="courses[]" value="{{ $course->CourseCode }}">
                                                    @endforeach
                                                    
                                                    <button type="submit" class="btn btn-success">Yes</button>
                                                </form>
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($isStudentsEligibleToRegister < 0)
                                {{-- Show ineligible modal --}}
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ineligibleModalRepeatCombined">Register</button>
                                <!-- Ineligible Modal -->
                                <div class="modal fade" id="ineligibleModalRepeatCombined" tabindex="-1" aria-labelledby="ineligibleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Not Eligible for Registration</h5>
                                            </div>
                                            <div class="modal-body">
                                                @if($studentsBalance < $totalRegistrationFee)
                                                    {{-- Insufficient funds for registration --}}
                                                    <p style="color:red;">You are short of the registration fee by: K {{ number_format(abs($isStudentsEligibleToRegister), 2) }}</p>
                                                    <p>Kindly make a payment to proceed with the registration.</p>
                                                @else
                                                    {{-- Other reason for ineligibility --}}
                                                    <p style="color:red;">You are not eligible for registration.</p>
                                                    <p>Please contact the accounts office for more information.</p>
                                                @endif
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
