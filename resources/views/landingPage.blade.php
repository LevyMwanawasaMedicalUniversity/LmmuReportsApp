@extends('layouts.app', [
    'namePage' => 'Dashboard',
    'class' => 'login-page sidebar-mini ',
    'activePage' => 'home',
    'backgroundImage' => asset('now') . "/img/DJI_0096.jpg",
])

@section('content')
  <div class="panel-header panel-header-lg">
    {{-- <canvas id="bigDashboardChart"></canvas> --}}
    <div class="content">
      <div class="card-body">
        <div class="table-responsive">
          <style>
            .table thead th {
                font-weight: bold;
                text-transform: uppercase;
            }
            /* .table thead {
                background-color: #f96332;
;
                color: #fff;
            } */
        
            .table tbody tr td:first-child {
                font-weight: bold;
                color: #f96332;
            }
        
            .table tbody tr:last-child td {
                font-weight: bold;
                background-color: #f5f5f5;
            }
          </style>
          <table class="table table-striped table-hover">
            <thead class="thead">
                <tr>
                  <th style="color: #fff; font-weight: bold;"><strong>Study Mode</strong></th>
                  <th style="color: #fff; font-weight: bold;"><strong>Edurole</strong></th>
                  <th style="color: #fff; font-weight: bold;"><strong>Sis Reports</strong></th>
                  <th style="color: #fff; font-weight: bold;"><strong>Total </strong></th>
                </tr>
                @php
                  $totalFulltimeRegistrations = $eduroleRegisteredStudents->where('StudyType', 'Fulltime')->count() + $sisReportsRegisteredStudents->where('StudyType', 'Fulltime')->count();
                  $totalDistanceRegistrations = $eduroleRegisteredStudents->where('StudyType', 'Distance')->count() + $sisReportsRegisteredStudents->where('StudyType', 'Distance')->count();
                @endphp
                
            </thead>
            <tbody>
                <tr>
                    <td style="color:#f96332;"><strong>Fulltime</strong></td>
                    
                    <td style="color:#f5f5f5;">{{ $eduroleRegisteredStudents->where('StudyType', 'Fulltime')->count() }}</td>
                    <td style="color:#f5f5f5;">{{ $sisReportsRegisteredStudents->where('StudyType', 'Fulltime')->count() }}</td>
                    <td style="background-color: #f5f5f5;">{{ $totalFulltimeRegistrations }}</td>
                </tr>
                <tr>
                    <td style="color:#f96332;"><strong>Distance</strong></td>

                    <td style="color:#f5f5f5;">{{ $eduroleRegisteredStudents->where('StudyType', 'Distance')->count() }}</td>
                    <td style="color:#f5f5f5;">{{ $sisReportsRegisteredStudents->where('StudyType', 'Distance')->count() }}</td>
                    <td style="background-color: #f5f5f5;">{{ $totalDistanceRegistrations }}</td>
                </tr>
                <tr>
                    <td style="background-color: #f5f5f5; font-weight: bold;">Total Registered</td>
                    @php
                        $totalRegistrations = $eduroleRegisteredStudents->count() + $sisReportsRegisteredStudents->count();
                        $eduroleTotalRegisteredStudents = $eduroleRegisteredStudents->count();
                        $sisReportsTotalRegisteredStudents = $sisReportsRegisteredStudents->count();
                    @endphp
                    
                    <td style="background-color: #f5f5f5;">{{ $eduroleTotalRegisteredStudents }}</td>
                    <td style="background-color: #f5f5f5;">{{ $sisReportsTotalRegisteredStudents }}</td>
                    <td style="background-color: #f5f5f5; font-weight: bold;">{{ $totalRegistrations }}</td>
                </tr>
            </tbody>
          </table>
        </div>
        
      </div>
    </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-lg-4">
        <div class="card card-chart">
          <div class="card-header">
            <h3 class="card-category"></h3>
            <h4 class="card-title">New Vs Returning</h4>
            {{-- <div class="dropdown">
              <button type="button" class="btn btn-round btn-outline-default dropdown-toggle btn-simple btn-icon no-caret" data-toggle="dropdown">
                <i class="now-ui-icons loader_gear"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <a class="dropdown-item" href="#">Something else here</a>
                <a class="dropdown-item text-danger" href="#">Remove Data</a>
              </div>
            </div> --}}
          </div>
          <div class="card-body" style="height: 400px;">
            <div class="chart-area" style="width:100%;height: 100%;">
                <canvas id="pieChartExample"></canvas>
            </div>
          </div>
        
        <script>
            var ctx = document.getElementById('pieChartExample').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['New Edurole', 'Returning Edulrole', 'Returning SISReports', 'New SISReports'],
                    datasets: [{
                      data: [
                            {{ $eduroleRegisteredStudents->where('StudentType', 'NEWLY ADMITTED')->count() }},
                            {{ $eduroleRegisteredStudents->where('StudentType', 'RETURNING STUDENT')->count() }},
                            {{ $sisReportsRegisteredStudents->where('StudentType', 'RETURNING STUDENT')->count()}},
                            {{ $sisReportsRegisteredStudents->where('StudentType', 'NEWLY ADMITTED')->count() }} 
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)', // color for 'New Edurole'
                            'rgba(54, 162, 235, 0.2)', // color for 'Returning Edulrole'
                            'rgba(255, 206, 86, 0.2)', // color for 'Returning SISReports'
                            'rgba(75, 192, 192, 0.2)'  // color for 'New SISReports'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)', // color for 'New Edurole'
                            'rgba(54, 162, 235, 1)', // color for 'Returning Edulrole'
                            'rgba(255, 206, 86, 1)', // color for 'Returning SISReports'
                            'rgba(75, 192, 192, 1)'  // color for 'New SISReports'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: ''
                        }
                    }
                },
            });
        </script>
          <div class="card-footer">
            {{-- <div class="stats">
              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
            </div> --}}
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card card-chart">
          <div class="card-header">
            <h5 class="card-category"></h5>
            <h4 class="card-title">Fulltime Vs Distance</h4>
            {{-- <div class="dropdown">
              <button type="button" class="btn btn-round btn-outline-default dropdown-toggle btn-simple btn-icon no-caret" data-toggle="dropdown">
                <i class="now-ui-icons loader_gear"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <a class="dropdown-item" href="#">Something else here</a>
                <a class="dropdown-item text-danger" href="#">Remove Data</a>
              </div>
            </div> --}}
          </div>
          @php
          $totalFulltimeRegistrations = $eduroleRegisteredStudents->where('StudyType', 'Fulltime')->count() + $sisReportsRegisteredStudents->where('StudyType', 'Fulltime')->count();
          $totalDistanceRegistrations = $eduroleRegisteredStudents->where('StudyType', 'Distance')->count() + $sisReportsRegisteredStudents->where('StudyType', 'Distance')->count();
          @endphp
          <div class="card-body" style="height: 400px;">
            <div class="chart-area" style="height: 100%;">
              <canvas id="lineChartExampleWithNumbersAndGrid"></canvas>
            </div>
          </div>
          <script>
            var ctx = document.getElementById('lineChartExampleWithNumbersAndGrid').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Fulltime', 'Distance'],
                    datasets: [{
                        data: [
                            {{ $totalFulltimeRegistrations }},
                            {{ $totalDistanceRegistrations }}
                        ],
                        backgroundColor: [
                          'rgba(75, 192, 192, 0.2)', // color for 'Fulltime'
                          'rgba(153, 102, 255, 0.2)'  // color for 'Distance'
                      ],
                      borderColor: [
                          'rgba(75, 192, 192, 1)', // color for 'Fulltime'
                          'rgba(153, 102, 255, 1)'  // color for 'Distance'
                      ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: ''
                        }
                    }
                },
            });
        </script>
          <div class="card-footer">
            {{-- <div class="stats">
              <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
            </div> --}}
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card card-chart">
          <div class="card-header">
            <h5 class="card-category"></h5>
            <h4 class="card-title">Schools</h4>
          </div>
          @php
          $totalSchoolsSomcs = $eduroleRegisteredStudents->where('School', 'SOMCS')->count() + $sisReportsRegisteredStudents->where('School',  'SOMCS')->count();
          $totalSchoolsSON = $eduroleRegisteredStudents->where('School', 'SON')->count() + $sisReportsRegisteredStudents->where('School',  'SON')->count();
          $totalSchoolsSOPHES = $eduroleRegisteredStudents->where('School', 'SOPHES')->count() + $sisReportsRegisteredStudents->where('School',  'SOPHES')->count();
          $totalSchoolsDRGS = $eduroleRegisteredStudents->where('School', 'DRGS')->count() + $sisReportsRegisteredStudents->where('School',  'DRGS')->count();
          $totalSchoolsIBBS = $eduroleRegisteredStudents->where('School', 'IBBS')->count() + $sisReportsRegisteredStudents->where('School',  'IBBS')->count();
          $totalSchoolsSOHS = $eduroleRegisteredStudents->where('School', 'SOHS')->count() + $sisReportsRegisteredStudents->where('School',  'SOHS')->count();
          @endphp
          <div class="card-body" style="height: 400px;">
            <div class="chart-area" style="height: 100%;">
              <canvas id="barChartSimpleGradientsNumbers"></canvas>
            </div>
          </div>
          <script>
            var ctx = document.getElementById('barChartSimpleGradientsNumbers').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['SOMCS', 'SON', 'SOPHES', 'DRGS', 'IBBS', 'SOHS'],
                    datasets: [{
                        data: [
                            {{ $totalSchoolsSomcs }},
                            {{ $totalSchoolsSON }},
                            {{ $totalSchoolsSOPHES }},
                            {{ $totalSchoolsDRGS }},
                            {{ $totalSchoolsIBBS }},
                            {{ $totalSchoolsSOHS }}
                        ],
                        backgroundColor: [
                          'rgba(255, 99, 132, 0.2)',  // Red
                          'rgba(255, 159, 64, 0.2)',  // Orange
                          'rgba(255, 205, 86, 0.2)',  // Yellow
                          'rgba(75, 192, 192, 0.2)',  // Green
                          'rgba(54, 162, 235, 0.2)',  // Blue
                          'rgba(153, 102, 255, 0.2)'  // Purple
                      ],
                      borderColor: [
                          'rgba(255, 99, 132, 1)',  // Red
                          'rgba(255, 159, 64, 1)',  // Orange
                          'rgba(255, 205, 86, 1)',  // Yellow
                          'rgba(75, 192, 192, 1)',  // Green
                          'rgba(54, 162, 235, 1)',  // Blue
                          'rgba(153, 102, 255, 1)'  // Purple
                      ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: ''
                        }
                    }
                },
            });
        </script>
          <div class="card-footer">
            <div class="stats">
              {{-- <i class="now-ui-icons ui-2_time-alarm"></i> Last 7 days --}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card  card-tasks">
          <div class="card-header ">
            <h5 class="card-category"></h5>
            <h4 class="card-title">Programmes</h4>
          </div>
          @php
            $eduroleRegisteredStudentsProgrammes = $eduroleRegisteredStudents->groupBy('ProgrammeCode');
            $sisReportsRegisteredStudentsProgrammes = $sisReportsRegisteredStudents->groupBy('ProgrammeCode');

            $eduroleProgrammeCounts = $eduroleRegisteredStudentsProgrammes->map->count();
            $sisReportsProgrammeCounts = $sisReportsRegisteredStudentsProgrammes->map->count();
        @endphp
          <div class="card-body" style="height: 600px;">
            <div class="chart-area" style="height: 100%; width: 100%;" >
                <canvas id="barChartSimpleGradientsNumbersProgrammes" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>
          <script>
            var ctx = document.getElementById('barChartSimpleGradientsNumbersProgrammes').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($eduroleProgrammeCounts->keys()),
                    datasets: [{
                        label: 'Edurole',
                        data: @json($eduroleProgrammeCounts->values()),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }, {
                        label: 'SIS Reports',
                        data: @json($sisReportsProgrammeCounts->values()),
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: ''
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            });
        </script>
          <div class="card-footer ">
            <hr>
            {{-- <div class="stats">
              <i class="now-ui-icons loader_refresh spin"></i> Updated 3 minutes ago
            </div> --}}
          </div>
        </div>
      </div>
      {{-- <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5 class="card-category">All Persons List</h5>
            <h4 class="card-title"> Employees Stats</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table">
                <thead class=" text-primary">
                  <th>
                    Name
                  </th>
                  <th>
                    Country
                  </th>
                  <th>
                    City
                  </th>
                  <th class="text-right">
                    Salary
                  </th>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      Dakota Rice
                    </td>
                    <td>
                      Niger
                    </td>
                    <td>
                      Oud-Turnhout
                    </td>
                    <td class="text-right">
                      $36,738
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Minerva Hooper
                    </td>
                    <td>
                      Curaçao
                    </td>
                    <td>
                      Sinaai-Waas
                    </td>
                    <td class="text-right">
                      $23,789
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Sage Rodriguez
                    </td>
                    <td>
                      Netherlands
                    </td>
                    <td>
                      Baileux
                    </td>
                    <td class="text-right">
                      $56,142
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Doris Greene
                    </td>
                    <td>
                      Malawi
                    </td>
                    <td>
                      Feldkirchen in Kärnten
                    </td>
                    <td class="text-right">
                      $63,542
                    </td>
                  </tr>
                  <tr>
                    <td>
                      Mason Porter
                    </td>
                    <td>
                      Chile
                    </td>
                    <td>
                      Gloucester
                    </td>
                    <td class="text-right">
                      $78,615
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> --}}
    </div>
  </div>
@endsection

@push('js')
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      demo.initDashboardPageCharts();

    });
  </script>
@endpush