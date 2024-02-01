@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Academics Queries',
    'activePage' => 'academics',
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
            <h4 class="card-title">Exam Modification Audit Trail</h4>
          </div>
          <div class="toolbar">
              
           
          
            <div class="row align-items-center">
                <div class="col-md-5 ml-3">
                <div class="form-group">
                            <label for="filterInput">Filter Table</label>
                            {{-- <input type="text" name="course-code" class="form-control" id="course-code" placeholder="Enter cousrse code"> --}}
                            <input type="text" name="filterInput" id="filterInput"class="form-control" placeholder="Filter by course code or course name...">
                        </div>
                </div>
                <div class="col-md-6">
                    @if(!empty($results))
                    <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('exportAllCoursesWithResults') }}">Export Entire List</a>
                    @endif
                </div>
            </div>
        
        </div>
            @if(!empty($results))
            
            <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>Student Number</th>
                            <th>Course  Code</th>
                            <th>C/A</th>
                            <th>Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>Submitted By</th>
                            <th>Reviewed By</th>
                            <th>Approved By</th>
                            <th>Type Of Change</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{$result->StudentID}}</td>
                            <td>{{$result->CID}}</td>
                            <td>{{$result->CA}}</td>
                            <td>{{$result->Exam}}</td>
                            <td>{{$result->Total}}</td>
                            <td>{{$result->Grade}}</td>
                            <td>{{$result->SubmittedByFirstName}} {{$result->SubmittedBySurname}}</td>
                            <td>{{$result->ReviewedByFirstName}} {{$result->ReviewedBySurname}}</td>
                            <td>{{$result->ApprovedByFirstName}} {{$result->ApprovedBySurname}}</td>
                            <td>{{$result->Type}}</td>
                            <td>{{$result->DateTime}}</td>                              
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                
            </div>
            
            @else
            <div class="card-body text-center">
                <h3>Please Select An Option To Generate A Report.</h3>
            </div>
            @endif
        </div>
      </div>      
    </div>
  </div>
  <script>
    // Get the input element and table
    var filterInput = document.getElementById('filterInput');
    var table = document.querySelector('.table');

    // Add an input event listener to the filter input
    filterInput.addEventListener('input', function () {
        var filter = filterInput.value.toLowerCase(); // Convert input to lowercase for case-insensitive search

        // Get all table rows (except the header row)
        var rows = table.querySelectorAll('tbody tr');

        // Loop through each row and hide/show based on the filter query
        rows.forEach(function (row) {
            var courseCode = row.querySelector('td:nth-child(2)').textContent.toLowerCase(); // Get the course code
            var courseName = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Get the course name
            var studentNumber = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            var date = row.querySelector('td:nth-child(11)').textContent.toLowerCase();
            if (courseCode.includes(filter) || courseName.includes(filter) || studentNumber.includes(filter) || date.includes(filter)) {
                row.style.display = 'table-row'; // Show the row
            } else {
                row.style.display = 'none'; // Hide the row
            }
        });
    });
</script>

@endsection