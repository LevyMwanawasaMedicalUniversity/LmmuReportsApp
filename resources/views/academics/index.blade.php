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
            <h4 class="card-title"> Academics Queries</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                      <tr>
                        <th>Querie Name</th>
                        <th>Querie Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><a href="{{ route('examMdificationAuditTrail') }}">Grades modification Audit Trail</a></td>
                        <td>View Grades Modified And The Details of The Modification</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewAllCoursesAttachedToProgramme') }}">View All Courses Mapped To Programmes</a></td>
                        <td>View all courses on Edurole attached to respective programmes, year of study and study mode</td>
                      </tr>                      
                      <tr>
                        <td><a href="{{ route('viewAllCoursesWithResults') }}">Course With Results 2023</a></td>
                        <td>View All Courses With 2023 Results</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewAllStudentsRegisteredInASpecificAcademicYear') }}">Students Registered Year</a></td>
                        <td>All students registered in a specific academic Year, regardless of programme</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewAllProgrammesPerSchool') }}">Program Per School</a></td>
                        <td>List of programming available in a specified school</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewStudentsFromSpecificIntakeYearTakingAProgramme') }}">Students from specific intake year taking a programme</a></td>
                        <td>This generates a report of students from an intake year that are currently in a specified program, regardless of whether or not they are registered. For example, all 2022 intake (220) currently in Diploma Clinical Medical Sciences General (DipCMSG)</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme') }}">Registered Students from specific intake year taking a programme</a></td>
                        <td>This generates a report of REGISTERED students from an intake year that are currently in a specified program. For example, all Registered 2022 intake (220) currently in Diploma Clinical Medical Sciences General (DipCMSG)</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewRegisteredStudentsPerYearInYearOfStudy') }}">Registered Students per year in year of study</a></td>
                        <td>This is a report of students registered in a specific academic and in a specific year of study. For example, if you want to see a list of first-year students that registered in 2022</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy') }}">Registered students in a specific programme and their year of study</a></td>
                        <td>For example, if your goal is to pull all registered Fourth YEAR students taking MBCHB</td>
                      </tr>
                      <tr>
                        <td><a href="{{ route('viewRegisteredAndUnregisteredPerYear') }}">Registered and Unregistered Students per Year</a></td>
                        <td>Generate a list of students with and without a registration in a specified academic year.</td>
                      </tr>
                    </tbody>
                  </table>
            </div>
          </div>
        </div>
      </div>      
    </div>
  </div>

@endsection