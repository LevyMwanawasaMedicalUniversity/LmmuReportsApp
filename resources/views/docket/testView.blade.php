@extends('layouts.app', [
    'namePage' => 'studentExaminationDocket',
    'class' => 'sidebar-mini',
    'activePage' => 'studentExaminationDocket',
    'activeNav' => '',
])

@section('content')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    #docketCard {
        max-width: 1024px; /* Maintain maximum width for printing */
        margin: auto; /* Center card on the page */
    }
    
    img {
        max-width: 100%; /* Ensure images scale down */
        height: auto; /* Maintain aspect ratio */
    }
}
</style>
<div class="panel-header panel-header-sm"></div>

<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0" id="docketCard" style="max-width: 1024px; margin: auto;">
                <!-- Header -->
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="card-title no-print m-0">Student Examination Docket</h4>

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
                <div class="card-body p-4">
                    @php
                        $route = $studentResults->StudentID;
                        $url = ($route);
                    @endphp

                    <!-- Header Section with University Name, Logo, and QR Code -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <!-- University Logo -->
                        <a href="//edurole.lmmu.ac.zm">
                            <img src="{{$logoDataUri}}" id="universityLogo" style="width: 140px; height: 140px;" class="img-fluid" alt="LMMU Logo">
                        </a>

                        <!-- University Name and Exam Details -->
                        <div class="text-center flex-grow-1">
                            <h2 class="font-weight-bold m-0">Levy Mwanawasa Medical University</h2>
                        </div>

                        <!-- Student Image -->
                        <div class="text-right">
                            <div style="width: 140px; height: 140px;">
                                <img src="{{ $imageDataUri }}" id="studentPhoto" class="border border-dark" style="width: 100%; height: 100%; object-fit: cover;" alt="Student Photo">
                            </div>
                        </div>
                    </div>

                    <!-- Student Details -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <p class="m-0"><strong>{{$studentResults->FirstName}} {{$studentResults->Surname}}</strong></p>
                            <p class="m-0">StudentID: <strong>{{$studentResults->StudentID}}</strong></p>
                            <p class="m-0">NRC: <strong>{{$studentResults->GovernmentID}}</strong></p>
                            <p class="m-0">School: <strong>{{$studentResults->Description}}</strong></p>                            
                            <p class="m-0">Delivery Mode: <strong>{{$studentResults->StudyType}}</strong></p>
                        </div>

                        <!-- Exam Details -->
                        <div>
                            <small>FINAL EXAMINATION DOCKET 2024</small><br>
                            <small>PRINTED ON: <span id="currentDate"></span></small>
                        </div>

                        <!-- QR Code -->
                        <div>
                            <div style="width: 140px; height: 140px;">
                                {!! QrCode::size(140)->generate($url) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Course Information Section -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Course Details</h6>
                        <p>The student is studying <strong>{{$studentResults->Name}}</strong> and is authorized to write the following courses:</p>
                    </div>

                    <!-- Course Table -->
                    <div class="table-responsive mb-4">
                    <form id="myForm" action="" method="POST">
                    @csrf
                        <table class="table table-sm table-bordered" id="courseTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Date / Time</th>
                                    <th>Venue</th>
                                    <th>Signature Invigilator</th>
                                    <th class="no-print">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                <tr>
                                    <td>{{$course->Course}} - {{$course->Program}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <button class="block no-print" style="background-color: #dc3545; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;" type="button" onclick="removeRow(this)">Remove</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button class="block no-print" style="background-color: #28a745; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;" type="submit">Update</button>
                        </form>
                    </div>
                    <a href="{{ route('courses.select', $studentResults->StudentID) }}">
                        <button class="block no-print" style="background-color: #007bff; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Add Course(s)</button>
                    </a>

                    <!-- Important Information -->
                    <div class="text-muted small">
                        <p>Please check your courses against the timetable for the exact date and time of the examination.</p>
                        <p>Note: Admission into the hall requires a valid Student ID, NRC, or Passport, this slip, and fee clearance.</p>
                    </div>

                    <!-- Download PDF Button (hidden in PDF) -->
                    <div class="text-center mt-4 no-print">
                        <button class="btn btn-success btn-sm" id="downloadPdf">Download PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Include the html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>

<!-- Your custom script to handle PDF download -->
<script>
    document.getElementById('downloadPdf').addEventListener('click', function () {
        var element = document.getElementById('docketCard'); // Select the card element

        // Hide the "Download PDF" button
        document.querySelector('.no-print').style.display = 'none';

        // Convert images to base64 and replace the sources
        convertImagesToBase64(function() {
            // Customize options if needed
            var opt = {
                margin:       [0.5, 0.5, 0.5, 0.5],
                filename:     'student_examination_docket.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // Generate PDF
            html2pdf().from(element).set(opt).save().then(function() {
                // Show the "Download PDF" button again after saving the PDF
                document.querySelector('.no-print').style.display = 'block';
            });
        });
    });

    // Set current date in the template
    var currentDate = new Date();
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    document.getElementById('currentDate').textContent = day + '-' + month + '-' + year;

    // Function to convert images to base64
    function convertImagesToBase64(callback) {
        var images = document.querySelectorAll('img');
        var totalImages = images.length;
        var processedImages = 0;

        images.forEach(function(img) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var reader = new FileReader();
                reader.onloadend = function() {
                    img.src = reader.result;
                    processedImages++;
                    if (processedImages === totalImages) {
                        callback();
                    }
                }
                reader.readAsDataURL(xhr.response);
            };
            xhr.open('GET', img.src);
            xhr.responseType = 'blob';
            xhr.send();
        });
    }

        $('#myForm').on('submit', function(e) {
        e.preventDefault();
        

        var coursePairs = $('.course-pair');
        var dataArray = [];
        var studentId = $('#studentId').val(); // Get the studentId from the hidden input

        coursePairs.each(function(index, pair) {
            var courseInput = $(pair).find('input[name="courses[][Course]"]');
            var programInput = $(pair).find('input[name="courses[][Program]"]');

            var courseValue = courseInput.val();
            var programValue = programInput.val();

            if (courseValue && programValue) {
                dataArray.push({
                    Course: courseValue,
                    Program: programValue
                });
            }
        });

        var studentId = $('#studentsId').val();
        var url = '/docket/updateCourses/' + studentId;
        console.log('Student ID:', studentId);
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                dataArray: dataArray
            },
            success: function(response) {
                // Handle the success response from the server
                console.log(response);
                if (response.success) {
                    // Display a success message to the user
                    alert('Courses updated successfully.');
                } else {
                    // Display an error message to the user
                    alert('Courses updated successfully.');
                }
            },
            error: function(xhr, status, error) {
                // Handle the error
                console.log(error);
                // Display an error message to the user
                alert('Error: ' + error);
            }
        });
    });

    $(document).ready(function() {
      // Add Row
      

      document.getElementById("addRow").addEventListener("click", function () {
            var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
            var newRow = table.insertRow(table.rows.length);
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(1);
            var cell4 = newRow.insertCell(1);
            var cell5 = newRow.insertCell(4);

            cell1.innerHTML = '<div class="course-pair">' +
                '<input type="text" name="courses[][Course]" placeholder="Course Code" required>' +
                '<input type="text" name="courses[][Program]" placeholder="Course Name" required>' +
                '</div>';
            cell2.innerHTML ='<td style="border: 1px solid #ccc; padding: 5px; width: 250px; height: 35px;">&nbsp;</td>'
            cell3.innerHTML ='<td style="border: 1px solid #ccc; padding: 5px; width: 250px; height: 35px;">&nbsp;</td>'
            cell4.innerHTML ='<td style="border: 1px solid #ccc; padding: 5px; width: 250px; height: 35px;">&nbsp;</td>'
            cell5.innerHTML = '<button type="button" onclick="removeRow(this)">Remove</button>';
        });

        // Remove the clicked row from the table
        
    });
    function removeRow(button) {
            var row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
</script>

<script>
  // Get the current date
  var currentDate = new Date();

  // Get the day, month, and year
  var day = currentDate.getDate();
  var month = currentDate.getMonth() + 1; // Months are zero-based
  var year = currentDate.getFullYear();

  // Format the date as "dd-mm-yyyy"
  var formattedDate = day + '-' + month + '-' + year;

  // Find the HTML element with the id "currentDate" and set its text content to the formatted date
  document.getElementById('currentDate').textContent = formattedDate;
</script>

@endsection