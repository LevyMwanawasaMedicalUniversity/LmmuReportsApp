@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Docket',
    'activePage' => 'docket-courses',
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
                    <h4 class="card-title">Course</h4>
                    <br>
                    <h6>select required course(s), then click "Enter" or the "Save Changes" button at the bottom of the table to submit</h6>
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
                
                    <div class="table-responsive">
                        <form method="POST" action="{{route('courses.store',$studentId)}}">
                    
                            @csrf  
                            <table class="table">
                                <thead class="text-primary">
                                    <label for="filterInput">Filter Courses</label>
                                    <input type="text" name="filterInput" id="filterInput"class="form-control" placeholder="Filter by course code or course name...">
                                <tr>
                                    <th>Select</th>
                                    <th>Course Code</th>
                                    <th class="text-end">Course Name</th>
                                    
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($courses as $result)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="course[]" value="{{$result->id}}" class="subject">

                                    </td>
                                    <td>{{$result->course_code}}</td>
                                    <td class="text-end">{{$result->course_name}}</td>
                                    
                                    
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                    
                </div>
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

            if (courseCode.includes(filter) || courseName.includes(filter)) {
                row.style.display = 'table-row'; // Show the row
            } else {
                row.style.display = 'none'; // Hide the row
            }
        });
    });
</script>

@endsection