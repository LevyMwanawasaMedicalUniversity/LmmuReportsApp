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
            margin-top: 7px;
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
            margin-top: 7px;
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

        /* CSS for the QR code container */
        .qr-code {
            float: right; /* Float the QR code to the right */
            margin-top: 20px; /* Adjust the top margin as needed */
        }

        /* CSS for the "QR CODE IMAGE" text */
        .qr-code p {
            font-weight: bold; /* Add bold styling to the text */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">           
            <h2>FINAL EXAMINATION DOCKET 2023</h2>
            <h4>PART-TIME STUDENT</h4>
            <h6>SENT BY EMAIL</h6>
        </div>
        <br>
        @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
        @endphp

        {{-- Define the route or path you want to convert to a QR code --}}
        @php
            $route = '/verifyNmcz/'.$studentResults->StudentID; // Replace with your desired route or path
            $url = url($route); // This generates the complete URL including the base URL
            $base64QRCode = base64_encode(QrCode::size(150)->generate($url));
            $currentDate = (new DateTime())->format('Y-m-d');
            $imagePath = public_path('assets/img/logo2.png'); // Replace with the correct path to your image
            $base64Image = base64_encode(file_get_contents($imagePath));
        @endphp

        {{-- Generate a QR code using simplesoftwareio/simple-qrcode --}}
        <div class="header">
            <img src="data:image/png;base64, {{ $base64QRCode }}" style="margin-right: 40%;">
            <img src="data:image/png;base64, {{ $base64Image }}" alt="Logo" height="150" style="margin-left: 40%px;">
        </div>

        <div class="main-content">
            <div class="row">
            
                <!-- Student Info Column -->
                <div class="student-info">
                    <p>Examination slip for: <b>{{$studentResults->FirstName}} {{$studentResults->Surname}}</b></p>
                    <p>StudentID No.: <b>{{$studentResults->StudentID}}</b></p>
                    <p>NRC No.: <b>{{$studentResults->GovernmentID}}</b></p>
                </div>
                <div class="balance">
                    <p>Balance: <b>K {{$studentResults->Amount}}</b></p>
                    <p>Delivery: <b>{{$studentResults->StudyType}}</b></p>
                    <p>Sent On: <b>{{ $currentDate }}</b></p>
                </div>
               
            </div>
            <div class="row">
                <p style="font-weight: bold">The Student is Studying: {{$studentResults->Name}}
                
                    Candidate has been authorized to write FINAL EXAMINATION in the following courses:</p>
            </div>
            
            <div class="examination-slip">
                

                <div class="table-container">
                   
                </div>

                

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