@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Docket',
    'activePage' => 'docket-indexNmcz',
    'activeNav' => '',
])

@section('content')
<style>
@media print {
    .no-print {
        display: none;
    }
}


</style>
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title no-print">View Student</h4>

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
                    <div style="width: 850px; height: 700px; position: centre; margin-top: 50px; margin-bottom: 30px; ">
                        <div style="float: left; width: 800px; position: relative; ">
                            <div style="position: absolute; right: 10px; font-size: 10pt; top: 100px;">
                                {{-- <img src="/datastore/output/secure/230200632-2023-10-28-96653.png"><br>230200632-2023-10-28-297369 --}}
                            </div>

                            {{-- Include the QR code generator --}}
                           {{-- Include the QR code generator --}}
                            @php
                            use SimpleSoftwareIO\QrCode\Facades\QrCode;
                            @endphp

                            {{-- Define the route or path you want to convert to a QR code --}}
                            @php
                                $route = '/verifyNmcz/'.$studentResults->StudentID; // Replace with your desired route or path
                                $url = url($route); // This generates the complete URL including the base URL
                            @endphp

                            {{-- Generate a QR code using simplesoftwareio/simple-qrcode --}}
                            

                            {{-- Display the QR code image --}}
                            
                            
                            <div style="width: 155px; height: 150px; padding-left: 30px; float: left;">
                                <a href="//edurole.lmmu.ac.zm">
                                    <img height="100px" src="//edurole.lmmu.ac.zm/templates/mobile/images/header.png">
                                </a>
                            </div>
                            

                            <div style="float: left; font-size: 18pt; color: #000; margin-top: 15px; width: 500px; ">
                                <span style="font-size: 22pt;">Levy Mwanawasa Medical University</span>
                                <div style="font-size: 15pt; font-weight: bold;">FINAL EXAMINATION DOCKET 2023 </div>
                                <div style="font-size: 15pt; font-weight: bold;">PART-TIME STUDENT </div>
                                <div style="font-size: 10pt; font-weight: bold;">PRINTED OUT</h2>
                                <BR>
                                    <BR>

                            </div>

                            
                        </div>
                        <div class="row justify-content-between">
                            <div style="width: 800px; margin-left: 20px; margin-top: 20px;">
                                {{-- <div style="width: 107px; float: left; margin-right: 20px; border: 1px solid #000;"> 
                                <img width="100%" src="//edurole.lmmu.ac.zm/datastore/identities/pictures/230200632.png">
                                </div> --}}
                                <div style="float: left; width: 300px; ">
                                    Examination slip for: <b>{{$studentResults->FirstName}} {{$studentResults->Surname}} </b> 
                                    <br> StudentID No.: <b>{{$studentResults->StudentID}}</b>
                                    <br> NRC No.: <b>{{$studentResults->GovernmentID}}</b>
                                    <p>Printed: <b><span id="currentDate"></span></b></p>
                                    Balance: <b>K {{$studentResults->Amount}}</b>
                                    <br> Delivery: <b>{{$studentResults->StudyType}}</b>
                                </div>
                                
                            </div>

                            <div style="width: 155px; height: 150px; padding-left: 30px; float: left;">
                                {!! QrCode::size(150)->generate($url ) !!}

                            </div>
                        </div>

                        <div style="clear: both; width: 800px; margin-left: 20px; padding-top: 20px;">
                            The Student is Studying: <b>{{$studentResults->Name}}</b><br>

                            <b>Candidate has been authorized to write FINAL EXAMINATION in the following courses: </b>
                        </div>
                        <div style="float: left; width: 400px;">
                        </div>
                        <div style="width: 100%; margin-left: 20px; margin-top: 20px;">
                            <table style="border: 1px solid #ccc; padding: 5px; width: 800px;">
                            <input type="hidden" class="studentsId" id="studentsId" name="studentsId" value="{{$studentResults->StudentID}}">
                                <form id="myForm" action="" method="POST">
                                    @csrf
                                    <table id="myTable">
                                        <thead>
                                            <tr>
                                                <th style="border: 1px solid #ccc; padding: 5px;">Course</th>
                                                <th style="border: 1px solid #ccc; padding: 5px;"><b>DATE / TIME</b></th>
                                                <th style="border: 1px solid #ccc; padding: 5px;"><b>VENUE</b></th>
                                                <th style="border: 1px solid #ccc; padding: 5px;"><b>SIGNATURE INVIGILATOR</b></th>
                                                <th style="border: 1px solid #ccc; padding: 5px;" class="no-print">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courses as $course)
                                            <tr>
                                                <td style="border: 1px solid #ccc; padding: 5px;">
                                                    <div class="course-pair">
                                                        {{$course->CourseID}} 
                                                    </div>
                                                </td>
                                                <td style="border: 1px solid #ccc; padding: 5px; width: 180px; height: 35px;">&nbsp; </td>
                                                <td style="border: 1px solid #ccc; padding: 5px; width: 200px; height: 35px;">&nbsp; </td>
                                                <td style="border: 1px solid #ccc; padding: 5px; width: 200px; height: 35px;">&nbsp;</td>
                                                <td>
                                                    <button class="block no-print" style="background-color: #dc3545; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;" type="button" onclick="removeRow(this)">Remove</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <button class="block no-print" style="background-color: #28a745; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;" type="submit">Update</button>
                                </form>
                            </table>
                            
                            <div style="font-size: 10px; padding-top: 20px; float: left;"> 
                                Kindly cross-check your courses on this slip against the separate examination timetable for the EXACT date and time of the examination.<br>
                                VERY IMPORTANT: Admission into the Examination Hall will be STRICTLY by STUDENT IDENTITY CARD, NRC OR PASSPORT, this EXAMINATION CONFIRMATION SLIP, and clearance of all OUTSTANDING TUITION FEES.<br><center>
                                <button class="block no-print" style="background-color: #28a745; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 150px; height: 40px;" size="100" onclick="printContent()">Print Docket</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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