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

        <div class="accordion" id="coursesAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <span class="ms-auto">{{ $studentDetails->Name }}</span>
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
                                            <input type="checkbox" name="courseRepeat[]" value="{{$course->CourseCode}}" class="courseRepeat" checked>
                                        </td>
                                        <td>{{$course->course_code}}</td>
                                        <td class="text-end">{{$course->CourseName}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary registerButtonRepeat">Register</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            var registrationFeesRepeatArray = {!! json_encode($registrationFeesRepeat) !!};
            var totalFeeArrayRepeat = {!! json_encode($totalFeesRepeat) !!};
        </script>
    </div>
</div> 
<!-- </div> -->
