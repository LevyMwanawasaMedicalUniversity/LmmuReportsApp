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
                                                $course = $courses->first();
                                                $amount = $course->InvoiceAmount;
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