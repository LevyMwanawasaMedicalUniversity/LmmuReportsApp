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
            $registrationFeesRepeat = [];
            $totalFeesRepeat = [];
        @endphp

        <div class="accordion" id="coursesAccordion{{ $studentId }}"> <!-- Concatenate 2024 with loop index -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}{{ $studentId }}" aria-expanded="false" aria-controls="collapse{{$loop->index}}{{ $studentId }}">
                        <span class="ms-auto">{{ $programme }}</span>
                        @php
                            $sisInvoices = \App\Models\SageInvoice::where('Description','=',$programme)->first();
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
                    
                        <span class="ms-auto registrationFeeRepeat" id="registrationFeeRepeat{{ $studentId }}">Registration Fee = K{{ number_format($amount * 0.25, 2) }}</span>
                        {{-- <p>{{$numberOfRepeatCourses}}</p> --}}
                        <span class="ms-auto totalFeeRepeat" id="totalFeeRepeat{{ $studentId }}">Total Invoice = K{{ number_format($amount,0) }}</span>
                    </button>
                </h2>
                <div id="collapse{{ $studentId }}" class="accordion-collapse collapse" aria-labelledby="heading" data-bs-parent="#coursesAccordion{{ $studentId }}">
                    <div class="accordion-body">
                        {{-- <form method="post" action=""> --}}
                            @csrf
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>Select</th>
                                            <th>Course Code</th>
                                            <th class="text-end">Course Name</th>
                                            {{-- <th class="text-end">Program</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($courses as $course)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="courseRepeat[]" value="{{$course->CourseCode}}" class="courseRepeat" id="courseRepeat{{ $studentId }}" checked>
                                            </td>
                                            <td>{{$course->CourseCode}}</td>
                                            <td class="text-end">{{$course->CourseName}}</td>                                                            
                                            {{-- <td class="text-end">{{$course->Programme}}</td> --}}
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary registerButtonRepeat" id="registerButtonRepeat{{ $studentId }}">Register</button>
                        {{-- </form> --}}
                    </div>
                </div>
                @endisset
            </div>
        </div>
        
        <script>
            var registrationFeesRepeatArray = {!! json_encode($registrationFeesRepeat) !!};
            var totalFeeArrayRepeat = {!! json_encode($totalFeesRepeat) !!};
        </script>
    </div>
</div> 
<!-- </div> -->
