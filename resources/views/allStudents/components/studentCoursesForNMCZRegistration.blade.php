<!-- <div class="container"> -->
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
            $registrationFeesRepeat;
            $totalFeesRepeat;
        @endphp

        <div class="accordion" id="coursesAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                @php
                    $otherFee = 2625;

                    if($failed == 1){
                        $tuitionFee = ($amount - $otherFee) / $theNumberOfCourses;
                        $numberOfRepeatCourses = $courses->count();
                        $amount = ($tuitionFee * $numberOfRepeatCourses) + $otherFee;
                    }
                    $registrationFeesRepeat = round($amount * 0.25, 2);
                    $totalFeesRepeat = round($amount, 2);
                @endphp
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <span class="ms-auto">{{ $programmeStudyCode }}</span>
                        <span class="ms-auto registrationFeeRepeat" id="registrationFeeRepeat">Registration Fee : K{{ number_format($amount * 0.25, 2) }}</span>
                        <span class="ms-auto totalFeeRepeat" id="totalFeeRepeat">Total Invoice = K{{ number_format($amount,0) }}</span>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#coursesAccordion">
                    <div class="accordion-body">
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
                                            <input type="checkbox" name="coursesnmcz[]" value="{{$course->course_code}}" class="coursesnmcz" id="coursesnmcz" checked>
                                        </td>
                                        <td>{{$course->course_code}}</td>
                                        <td class="text-end">{{$course->CourseName}}</td>
                                        <td class="text-right">
                                        @if ((auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
                                        <form method="POST" action="{{ route('deleteCourseFromNMCZCourses.student') }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="courseId" value="{{$course->course_code}}">
                                            <input type="hidden" name="studentStatus" value="{{ $studentStatus }}">
                                            <input type="hidden" name="studentId" value="{{ $studentId }}">
                                            <input type="hidden" name="year" value="2024">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                        @endif
                                </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary registerButtonNMCZ">Register</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            var registrationFeesNMCZ = {{ $registrationFeesRepeat }};
            var totalFeeNMCZ = {{ $totalFeesRepeat }};
        </script>
    </div>
</div> 


<!-- </div> -->
