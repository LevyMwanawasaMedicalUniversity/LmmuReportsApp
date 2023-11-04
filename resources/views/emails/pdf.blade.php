<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Docket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
        }

        .logo {
            text-align: left;
        }

        .main-content {
            margin-top: 20px;
        }

        .student-info {
            float: left;
            width: 50%;
        }

        .balance {
            float: right;
            width: 50%;
            text-align: right;
        }

        .examination-slip {
            margin-top: 20px;
            text-align: left;
        }

        .student-image {
            float: left;
            width: 25%;
        }

        .courses-table {
            float: left;
            width: 75%;
        }

        .table-container {
            border: 1px solid #ccc;
            padding: 5px;
            width: 100%;
        }

        .table-header {
            background-color: #ccc;
        }

        table {
            width: 100%;
        }

        table td {
            padding: 5px;
            border: 1px solid #ccc;
        }

        .no-print {
            display: none;
        }

        .print-button {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Levy Mwanawasa Medical University</h1>
            <h2>FINAL EXAMINATION DOCKET 2023</h2>
            <h2>UNREGISTERED STUDENT</h2>
            <h2>SENT BY EMAIL</h2>
        </div>

        <div class="main-content">
            <div class="student-info">
                <p>Examination slip for: <b>{{$studentResults->FirstName}} {{$studentResults->Surname}}</b></p>
                <p>StudentID No.: <b>{{$studentResults->StudentID}}</b></p>
                <p>NRC No.: <b>{{$studentResults->GovernmentID}}</b></p>
            </div>

            <div class="balance">
                <p>Balance: <b>K {{$studentResults->Amount}}</b></p>
                <p>Delivery: <b>{{$studentResults->StudyType}}</b></p>
            </div>

            <div class="examination-slip">
                <p style="font-weight: bold">The Student is Studying: {{$studentResults->Name}}
                
                Candidate has been authorized to write FINAL EXAMINATION in the following courses:</p>

                <div class="table-container">
                    <table>
                        <thead class="table-header">
                            <tr>
                                <td>Course</td>
                                <td><b>DATE / TIME</b></td>
                                <td><b>VENUE</b></td>
                                <td><b>SIGNATURE INVIGILATOR</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                            <td style="width: 15%;">
                                <div class="course-pair">
                                    {{$course->Course}} - {{$course->Program}}
                                </div>
                            </td>
                            <td style="width: 28%;">&nbsp;</td>
                            <td style="width: 28%;">&nbsp;</td>
                            <td style="width: 28%;">&nbsp;</td>
                            
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('courses.select', $studentResults->StudentID) }}" class="no-print">
                    <button>Add Course(s)</button>
                </a>

                <div class="print-button">
                    Kindly cross-check your courses on this slip against the separate examination timetable for the EXACT date and time of the examination.
                    <br>VERY IMPORTANT: Admission into the Examination Hall will be STRICTLY by STUDENT IDENTITY CARD, NRC OR PASSPORT, this EXAMINATION CONFIRMATION SLIP, and clearance of all OUTSTANDING TUITION FEES.
                    <br>
                    <button class="block no-print" onclick="printContent()">Print Docket</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

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