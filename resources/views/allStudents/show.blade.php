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
                                        <th>Total Payments Made</th>
                                        <th>Total Payments in 2024</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $studentsPayments->Account }}</td>
                                        <td>{{ $studentsPayments->LatestInvoiceDate }}</td>
                                        <td>K{{ $studentsPayments->TotalPayments }}</td>
                                        <td>K{{ $studentsPayments->TotalPayment2024 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">YOUR COURSES FOR REGISTRATION</h4>
                        </div>
                        <div class="card-body">  
                            @foreach($currentStudentsCourses->groupBy('CodeRegisteredUnder') as $programme => $courses)
                            <div class="accordion" id="coursesAccordion{{$loop->index}}{{ $studentsPayments->Account }}"> <!-- Concatenate 2024 with loop index -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{$loop->index}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$loop->index}}{{ $studentsPayments->Account }}" aria-expanded="false" aria-controls="collapse{{$loop->index}}{{ $studentsPayments->Account }}">
                                            {{ $programme }}
                                            @php
                                                $course = $courses->first();
                                            @endphp
                                            <span class="ms-auto">K{{ $course->InvoiceAmount }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{$loop->index}}{{ $studentsPayments->Account }}" class="accordion-collapse collapse" aria-labelledby="heading{{$loop->index}}" data-bs-parent="#coursesAccordion{{$loop->index}}{{ $studentsPayments->Account }}">
                                        <div class="accordion-body">
                                            <form method="post" action="">
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
                                                                <input type="checkbox" name="course[]" value="{{$course->CourseCode}}" class="course" checked>
                                                            </td>
                                                            <td>{{$course->CourseCode}}</td>
                                                            <td class="text-end">{{$course->CourseName}}</td>
                                                            <td class="text-end">{{$course->Programme}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                <button type="submit" class="btn btn-primary">Register</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>                
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">COURSE REGISTRATION</h4>
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
                                            <span class="ms-auto">K{{ $course->InvoiceAmount }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{$loop->index}}" class="accordion-collapse collapse" aria-labelledby="heading{{$loop->index}}" data-bs-parent="#coursesAccordion{{$loop->index}}">
                                        <div class="accordion-body">
                                            <form method="post" action="">
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
                                                                <input type="checkbox" name="course[]" value="{{$course->CourseCode}}" class="course" checked>
                                                            </td>
                                                            <td>{{$course->CourseCode}}</td>
                                                            <td class="text-end">{{$course->CourseName}}</td>
                                                            <td class="text-end">{{$course->Programme}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                <button type="submit" class="btn btn-primary">Register</button>
                                            </form>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




@endsection