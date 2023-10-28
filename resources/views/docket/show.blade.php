@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Docket',
    'activePage' => 'docket-index',
    'activeNav' => '',
])

@section('content')
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">View Student</h4>

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
                <div style="width: 850px; height: 700px; position: relative; margin-top: 50px;  margin-bottom: 30px; ">
					<div style="float: left; width: 800px; position: relative; ">
						<div style="position: absolute;  right: 10px; font-size: 10pt; top: 100px;">
							<img src="/datastore/output/secure/230200632-2023-10-28-96653.png"><br>230200632-2023-10-28-297369
						</div>
			
						<div style="width: 155px; height: 150px;  padding-left: 30px; float: left;">
							<a href="//edurole.lmmu.ac.zm">
								<img height="100px" src="//edurole.lmmu.ac.zm/templates/mobile/images/header.png">
							</a>
						</div>
						
						<div style="float: left; font-size: 18pt; color: #000; margin-top: 15px; width: 500px; ">
								<span style="font-size: 22pt;">Levy Mwanawasa Medical University</span>
								<div style="font-size: 15pt; font-weight: bold;">FINAL EXAMINATION DOCKET 2023 </div>
						
						</div>
					</div>
					<div style="width: 800px; margin-left: 20px; margin-top: 20px;"><div style="width: 107px; float: left; margin-right: 20px; border: 1px solid #000;"> <img width="100%" src="//edurole.lmmu.ac.zm/datastore/identities/pictures/230200632.png"></div><div style="float: left; width: 300px; ">
							Examination slip for: <b>{{$studentResults->FirstName}} {{$studentResults->Surname}} </b> 
							<br> StudentID No.: <b>{{$studentResults->StudentID}}</b>
							<br> NRC No.: <b>{{$studentResults->GovernmentID}}</b>
						</div>
						<div style="float: left; width: 400px;">
                            <p>Printed: <b><span id="currentDate"></span></b></p>
							Balance: <b>K {{$studentResults->Amount}}</b>
							<br> Delivery: <b>{{$studentResults->StudyType}}</b>
						</div>
					</div>
					
					<div style="clear: both; width: 800px; margin-left: 20px; padding-top: 20px;">
					 The Sudent is Studying: <b>{{$studentResults->Name}}</b><br>
						
						<b>Candidate has been authorized to write FINAL EXAMINATION in the following courses: </b>
					</div>
					<div style="float: left; width: 400px;">
					</div>
					<div style="width: 600px; margin-left: 20px; margin-top: 20px;"><table style="border: 1px solid #ccc; padding: 5px;  width: 800px;">
                    <form action="{{ route('update.courses', ['studentId' => $studentResults->StudentID]) }}" method="POST">
                        @csrf

                        <table>
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Program</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <input type="text" name="courses[][Course]" value="{{ $course->Course }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="courses[][Program]" value="{{ $course->Program }}" required>
                                    </td>
                                    <td>
                                        <button type="button" class="remove-row">Remove</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="button" id="add-row">Add</button>
                        <button type="submit">Submit</button>
                    </form>
                    <div style="font-size: 10px; padding-top: 20px; float: left;"> 
                        Kindly cross check your courses on this slip against the separate examination timetable for the EXACT date and time of the examination.<br>
                        VERY IMPORTANT : Admission into the Examination Hall will be STRICTLY by STUDENT IDENTITY CARD, NRC OR PASSPORT, this EXAMINATION CONFIRMATION SLIP and clearance of all OUTSTANDING TUITION FEES.<br><center>
                            <button class="block" style="background-color:green; border-color:blue;height: 40px; width: 150px" size="100" onclick="window.print()">Click To Print Docket</button></center>
                        <div></div>
                    </div></div></div>

                        </div>
                        
                    </div>
        </div>
    </div>
</div>

<script>
    // Add new row
    document.getElementById('add-row').addEventListener('click', function () {
        const table = document.querySelector('table tbody');
        const newRow = document.querySelector('table tbody tr').cloneNode(true);

        // Clear input fields in the newly added row
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
            if (input.name.includes('Course')) {
                input.name = 'courses[][Course]';
            } else if (input.name.includes('Program')) {
                input.name = 'courses[][Program]';
            }
        });

        table.appendChild(newRow);
    });

    // Remove row
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
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