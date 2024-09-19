@extends('layouts.app', [
    'namePage' => 'studentExaminationDocket',
    'class' => 'sidebar-mini',
    'activePage' => 'studentExaminationDocket',
    'activeNav' => '',
])

@section('content')
<div class="panel-header panel-header-sm"></div>
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <!-- Header -->
                <div class="card-header bg-primary text-white text-center py-2">
                    <h5 class="card-title no-print m-0">Student Examination Docket</h5>

                    @if (session('success'))
                        <div class="alert alert-success py-1 my-2">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger py-1 my-2">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                <!-- Body -->
                <div class="card-body p-3">
                    @php
                        // Define the route or path you want to convert to a QR code
                        $route = $studentResults->StudentID;
                        $url = ($route); // Generates the complete URL
                    @endphp

                    <!-- Header Section with University Name, Logo, and QR Code -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- University Logo -->
                        <div>
                            <a href="//edurole.lmmu.ac.zm">
                                <img src="//edurole.lmmu.ac.zm/templates/mobile/images/header.png" height="50px" class="img-fluid" alt="LMMU Logo">
                            </a>
                        </div>

                        <!-- University Name and Exam Details -->
                        <div class="text-center">
                            <h6 class="font-weight-bold m-0">Levy Mwanawasa Medical University</h6>
                            <small>FINAL EXAMINATION DOCKET 2024</small><br>
                            <small>PRINTED ON: <span id="currentDate"></span></small>
                        </div>

                        <!-- QR Code -->
                        <div class="bg-light p-1 rounded">
                            {!! QrCode::size(70)->generate($url) !!}
                        </div>
                    </div>

                    <!-- Student Details and Profile Image -->
                    <div class="row mb-3">
                        <!-- Student Details -->
                        <div class="col-md-7">
                            <p class="m-0"><strong>{{$studentResults->FirstName}} {{$studentResults->Surname}}</strong></p>
                            <p class="m-0">StudentID: <strong>{{$studentResults->StudentID}}</strong></p>
                            <p class="m-0">NRC: <strong>{{$studentResults->GovernmentID}}</strong></p>
                            <p class="m-0">Balance: <strong>K {{$studentResults->Amount}}</strong></p>
                            <p class="m-0">Delivery Mode: <strong>{{$studentResults->StudyType}}</strong></p>
                        </div>

                        <!-- Profile Image -->
                        <div class="col-md-5 text-center">
                            <div class="border border-dark" style="width: 100px; height: 130px; margin: 0 auto;">
                                <img src="//edurole.lmmu.ac.zm/datastore/identities/pictures/{{ $studentResults->StudentID }}.png" style="width: 100%; height: 100%; object-fit: cover;" alt="Student Photo">
                            </div>
                        </div>
                    </div>

                    <!-- Course Information Section -->
                    <div class="mb-3">
                        <h6 class="m-0">Course Details</h6>
                        <p class="m-0">The student is studying <strong>{{$studentResults->Name}}</strong> and is authorized to write the following courses:</p>
                    </div>

                    <!-- Course Table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Date / Time</th>
                                    <th>Venue</th>
                                    <th>Signature Invigilator</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                <tr>
                                    <td>{{$course->Course}} - {{$course->Program}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Important Information -->
                    <div class="text-muted">
                        <p class="mb-1">Please check your courses against the timetable for the exact date and time of the examination.</p>
                        <p class="mb-0">Note: Admission into the hall requires a valid Student ID, NRC, or Passport, this slip, and fee clearance.</p>
                    </div>

                    <!-- Print Button -->
                    <div class="text-center mt-3 no-print">
                        <button class="btn btn-success btn-sm" onclick="printContent()">Print Docket</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printContent() {
    // Hide elements with the "no-print" class
    var noPrintElements = document.querySelectorAll('.no-print');
    noPrintElements.forEach(function (element) {
        element.style.display = 'none';
    });

    var contentToPrint = document.querySelector('.content').innerHTML;
    var printWindow = window.open('', '', 'width=600,height=600');
    
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print</title></head><body>' + contentToPrint + '</body></html>');
    printWindow.document.close();

    var images = printWindow.document.getElementsByTagName('img');
    var imagesLoaded = 0;

    for (var i = 0; i < images.length; i++) {
        images[i].onload = function() {
            imagesLoaded++;
            if (imagesLoaded === images.length) {
                // All images are loaded, now we can initiate the print
                printWindow.print();
                printWindow.close();
            }
        };
    }

    // Restore the display of "no-print" elements after printing
    noPrintElements.forEach(function (element) {
        element.style.display = 'block';
    });
}

// Get and format current date
var currentDate = new Date();
var day = currentDate.getDate();
var month = currentDate.getMonth() + 1;
var year = currentDate.getFullYear();
var formattedDate = day + '-' + month + '-' + year;
document.getElementById('currentDate').textContent = formattedDate;
</script>
@endsection