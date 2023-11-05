@extends('layouts.appVerify')
@section('content')
@if($studentResults)
<div class="content">
    <div class="row justify-content-center"> <!-- Center the content horizontally -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center"> <!-- Center the header text -->
                    <h4 class="card-title no-print text-success"><b>STUDENT FOUND</b></h4>
                    <!-- Alerts -->
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
                    <div class="text-center"> <!-- Center the inner content horizontally -->
                        <!-- QR Code -->
                        

                        {{-- <div style="width: 155px; height: 150px; margin: 0 auto;">
                            {!! $qrCode !!}
                        </div> --}}
                        <!-- University and Student Info -->
                        {{-- <div style="font-size: 18pt; color: #000; margin-top: 15px;">
                            <span style="font-size: 22pt;">Levy Mwanawasa Medical University</span>
                            <div style="font-size: 15pt; font-weight: bold;">FINAL EXAMINATION DOCKET 2023 </div>
                            <div style="font-size: 15pt; font-weight: bold;">UNREGISTERED STUDENT </div>
                            <div style="font-size: 15pt; font-weight: bold;">PRINTED FROM ACADEMICS OFFICE</div>
                        </div> --}}
                        
                        <!-- Student Info -->
                        <div style="width: 300px; margin: 0px auto;">
                            Examination slip for: <b>{{$studentResults->FirstName}} {{$studentResults->Surname}}</b><br>
                            StudentID No.: <b>{{$studentResults->StudentID}}</b><br>
                            NRC No.: <b>{{$studentResults->GovernmentID}}</b><br>
                            Balance: <b>K {{$studentResults->Amount}}</b><br>
                            Delivery: <b>{{$studentResults->StudyType}}</b>
                            <table id="myTable">
                                <thead>
                                    <tr>
                                        <th style="border: 1px solid #ccc; padding: 5px;">Courses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                    <tr>
                                        <td style="border: 1px solid #ccc; padding: 5px;">
                                            <div class="course-pair">
                                                {{$course->Course}} - {{$course->Program}}
                                                <input type="hidden" name="courses[][Course]" value="{{$course->Course}}">
                                                <input type="hidden" name="courses[][Program]" value="{{$course->Program}}">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Course Table -->
                        <div style="width: 100%; margin: 20px auto;">
                            <table style="border: 1px solid #ccc; padding: 5px; width: 100%;">
                                <input type="hidden" class="studentsId" id="studentsId" name="studentsId" value="{{$studentResults->StudentID}}">
                                <form id="myForm" action="" method="POST">
                                    @csrf
                                    
                                </form>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<div class="content">
    <div class="row justify-content-center"> <!-- Center the content horizontally -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-center"> <!-- Center the header text -->
                    <h4 class="card-title no-print text-danger"><b>STUDENT NOT FOUND ON SYSTEM</b></h4>
                </div>
            </div>
        </div>
    </div>
</div>


@endif
@endsection
