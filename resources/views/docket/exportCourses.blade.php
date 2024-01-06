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
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .main-content {
            margin-top: 20px;
        }

        .table-container {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td, table th {
            padding: 10px;
            border: 1px solid #ccc;
        }

        table thead {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">           
            <h1>{{ $courseName }} - {{ $courseCode }} </h1><h3>EXAMINATION LIST</h3>
        </div>

        <div class="main-content">
            <div class="row" style="display: flex; justify-content: space-between; align-items: center;">
                @php
                $imagePath = public_path('assets/img/logo2.png'); // Replace with the correct path to your image
                $base64Image = base64_encode(file_get_contents($imagePath));
                @endphp
                <div>
                    <img src="data:image/png;base64,{{ $base64Image }}" alt="LMMU" height="100" style="margin-left: 40%;">                    
                </div>
            </div>
            <div class="row" style="display: flex; justify-content: center; align-items: center;">
                <!-- Student Info Column -->
                <div class="student-info">
                    
                </div>
            </div>
            <div class="row" >
                <!-- Date Column -->
                <div class="col">
                    <h5>DATE:</h5>
                    
                </div>
                <div class="col">
                    {{-- <h5>TIME:</h5> --}}
                </div>
                <div class="col">
                    <h5>TIME:</h5>
                </div>                
            </div>
                        
            
            <div class="examination-slip">
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>STUDENT NUMBER</th>
                                <th>SCHOOL</th>
                                <th>PROGRAM</th>
                                {{-- <th>COURSE CODE</th> --}}
                                <th>SIGNATURE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td style="max-width: 30%;">
                                    <div class="course-pair">
                                        {{ $student->StudentID }}
                                    </div>
                                </td>
                                <td style="width: 30%;">
                                    <div class="course-pair">
                                        {{ $student->Description }}
                                    </div>
                                </td>
                                <td style="width: 30%;">
                                    <div class="course-pair">
                                        {{ $student->Name }}
                                    </div>
                                </td>
                                {{-- <td style="width: 30%;">
                                    <div class="course-pair">
                                        ahhh
                                    </div>
                                </td> --}}
                                <td style="width: 20%;">&nbsp;</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>