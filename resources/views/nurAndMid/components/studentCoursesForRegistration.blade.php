<!-- <div class="container"> -->
<div class="card">
    <div class="card-header">

        <h4 class="card-title">YOUR COURSES FOR REGISTRATION</h4>

    </div>
    <div class="card-body">  
        @php
            $registrationFeesRepeat = [];
            $totalFeesRepeat = [];
        @endphp
        @foreach($groupedCoursesAll as $programme => $courses)
            <div class="accordion" id="coursesAccordion{{$loop->index}}{{ $courses->first()['Student'] }}"> <!-- Concatenate student ID with loop index -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{$loop->index}}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}{{ $courses->first()['Student'] }}" aria-expanded="false" aria-controls="collapse{{$loop->index}}{{ $courses->first()['Student'] }}">
                            <span class="ms-auto">{{ $programme }}</span>
                            
                    @isset($sisInvoices)
                        
                            <span class="ms-auto registrationFeeRepeat" id="registrationFeeRepeat{{$loop->index}}{{ $courses->first()['Student'] }}">Registration Fee = K{{ number_format($amount * 0.25, 2) }}</span>
                            <span class="ms-auto totalFeeRepeat" id="totalFeeRepeat{{$loop->index}}{{ $courses->first()['Student'] }}">Total Invoice = K{{ number_format($amount,0) }}</span>
                        </button>
                    </h2>
                    <div id="collapse{{$loop->index}}{{ $courses->first()['Student'] }}" class="accordion-collapse collapse" aria-labelledby="heading{{$loop->index}}" data-bs-parent="#coursesAccordion{{$loop->index}}{{ $courses->first()['Student'] }}">
                        <div class="accordion-body">
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
                                                    <input type="checkbox" name="courseRepeat[]" value="{{$course['Course']}}" class="courseRepeat" id="courseRepeat{{$loop->parent->index}}{{ $courses->first()['Student'] }}{{$loop->index}}" checked>
                                                </td>
                                                <td>{{$course['Course']}}</td>
                                                <td class="text-end">{{$course['Grade']}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary registerButtonRepeat" id="registerButtonRepeat{{$loop->index}}{{ $courses->first()['Student'] }}">Register</button>
                        </div>
                    </div>
                    @endisset
                </div>
            </div>
        @endforeach
        <script>
            var registrationFeesRepeatArray = {!! json_encode($registrationFeesRepeat) !!};
            var totalFeeArrayRepeat = {!! json_encode($totalFeesRepeat) !!};
        </script>
    </div>
</div> 
<!-- </div> -->
