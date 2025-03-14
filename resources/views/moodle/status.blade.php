@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Moodle Status Dashboard</div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4>System Status Overview</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Students</h5>
                                            <p class="card-text h2">{{ $totalStudents }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Moodle Accounts</h5>
                                            <p class="card-text h2">{{ $totalMoodleAccounts }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Total Enrollments</h5>
                                            <p class="card-text h2">{{ $totalEnrollments }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4>Student Lookup</h4>
                            <form method="GET" action="{{ url('/moodle/status') }}">
                                <div class="input-group mb-3">
                                    <input type="text" name="student_id" class="form-control" placeholder="Enter Student ID" value="{{ $studentId ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="submit">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if ($studentId)
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Student Information</h4>
                                @if ($studentInfo)
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $studentInfo->FirstName }} {{ $studentInfo->Surname }}</h5>
                                            <p class="card-text"><strong>Student ID:</strong> {{ $studentInfo->ID }}</p>
                                            <p class="card-text"><strong>Email:</strong> {{ $studentInfo->PrivateEmail }}</p>
                                            
                                            <div class="alert {{ $moodleAccount ? 'alert-success' : 'alert-danger' }} mt-3">
                                                <strong>Moodle Account:</strong> {{ $moodleAccount ? 'Exists' : 'Not Found' }}
                                            </div>
                                            
                                            @if ($moodleAccount)
                                                <div class="alert {{ $courseCount > 0 ? 'alert-success' : 'alert-warning' }} mt-3">
                                                    <strong>Course Enrollments:</strong> {{ $courseCount }} (Expected: {{ $expectedCoursesCount ?? 0 }} from Edurole)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if ($moodleAccount && $enrollments && $enrollments->count() > 0)
                                        <h5>Course Enrollments</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Course Name</th>
                                                        <th>Course Code</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($enrollments as $enrollment)
                                                        <tr>
                                                            <td>{{ $enrollment->fullname }}</td>
                                                            <td>{{ $enrollment->shortname }}</td>
                                                            <td>
                                                                @if ($enrollment->status == 0)
                                                                    <span class="badge badge-success">Active</span>
                                                                @else
                                                                    <span class="badge badge-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif ($moodleAccount)
                                        <div class="alert alert-warning">
                                            No course enrollments found for this student.
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-danger">
                                        Student not found with ID: {{ $studentId }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Students with Most Enrollments</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Enrollment Count</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($studentsWithMostEnrollments as $student)
                                            <tr>
                                                <td>{{ $student->username }}</td>
                                                <td>{{ $student->enrollment_count }}</td>
                                                <td>
                                                    <a href="{{ url('/moodle/status?student_id=' . $student->username) }}" class="btn btn-sm btn-info">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
