@extends('layouts.app', [
    'namePage' => 'Dashboard',
    'class' => 'login-page sidebar-mini ',
    'activePage' => 'home',
    'backgroundImage' => asset('now') . "/img/DJI_0096.jpg",
])

@section('content')
  <div class="panel-header panel-header-lg">
    <div class="content">
      <div class="card-body">
        <div class="table-responsive">
          <style>
            /* Original table styles */
            .table thead th {
                font-weight: bold;
                text-transform: uppercase;
            }
            .table tbody tr td:first-child {
                font-weight: bold;
                color: #f96332;
            }
            .table tbody tr:last-child td {
                font-weight: bold;
                background-color: #f5f5f5;
            }
            .loading {
                text-align: center;
                font-size: 1.5em;
                color: #f96332;
            }
            
            /* Enhanced chart styles */
            .chart-area {
                padding: 15px;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                background-color: #fff;
            }
            .card-chart {
                border-radius: 5px;
                box-shadow: 0 5px 12px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
                overflow: hidden;
            }
            .card-title {
                font-weight: 600;
                margin-bottom: 0;
            }
            canvas {
                max-width: 100%;
                height: auto !important;
            }
          </style>
          <div id="loadingTable" class="loading">Loading...</div>
          <table class="table table-striped table-hover" style="display: none;">
            <thead class="thead">
                <tr>
                  <th style="color: #fff; font-weight: bold;"><strong>Study Mode</strong></th>
                  <th style="color: #fff; font-weight: bold;"><strong>Edurole</strong></th>
                  <th style="color: #fff; font-weight: bold;"><strong>Sis Reports</strong></th>
                  <th style="color: #fff; font-weight: bold;"><strong>Total </strong></th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
                <!-- Data will be inserted here via JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-lg-3 col-md-6">
        <div class="card card-chart">
          <div class="card-header">
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
            <h4 class="card-title">New Vs Returning</h4>
          </div>
          <div class="card-body" style="height: 400px;">
            <div class="chart-area" style="width:100%;height: 100%; position: relative;">
                <canvas id="pieChartExample"></canvas>
                <div class="loading" id="loadingPieChartExample">Loading...</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card card-chart">
          <div class="card-header">
            <h4 class="card-title">Fulltime Vs Distance</h4>
          </div>
          <div class="card-body" style="height: 400px;">
            <div class="chart-area" style="width:100%;height: 100%; position: relative;">
                <canvas id="lineChartExampleWithNumbersAndGrid"></canvas>
                <div class="loading" id="loadingLineChartExampleWithNumbersAndGrid">Loading...</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card card-chart">
          <div class="card-header">
            <h4 class="card-title">Schools</h4>
          </div>
          <div class="card-body" style="height: 400px;">
            <div class="chart-area" style="width:100%;height: 100%; position: relative;">
                <canvas id="barChartSimpleGradientsNumbers"></canvas>
                <div class="loading" id="loadingBarChartSimpleGradientsNumbers">Loading...</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="card card-chart">
          <div class="card-header">
            <h4 class="card-title">Year Of Study</h4>
          </div>
          <div class="card-body" style="height: 400px;">
            <div class="chart-area" style="width:100%;height: 100%; position: relative;">
                <canvas id="barChartSimpleGradientsNumbersYearOfStudy"></canvas>
                <div class="loading" id="loadingBarChartSimpleGradientsNumbersYearOfStudy">Loading...</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card  card-tasks">
          <div class="card-header ">
            <h4 class="card-title">Programmes</h4>
          </div>
          <div class="card-body" style="height: 500px;">
            <div class="chart-area" style="height: 100%; width: 100%; position: relative;">
              <div style="height: 100%;">
                <canvas id="barChartSimpleGradientsNumbersProgrammes" style="width: 100%; height: 100%;"></canvas>
                <div class="loading" id="loadingBarChartSimpleGradientsNumbersProgrammes">Loading...</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const academicYear = 2025;
    
    const loadingElements = [
      'loadingTable',
      'loadingPieChartExample',
      'loadingLineChartExampleWithNumbersAndGrid',
      'loadingBarChartSimpleGradientsNumbers',
      'loadingBarChartSimpleGradientsNumbersYearOfStudy',
      'loadingBarChartSimpleGradientsNumbersProgrammes'
    ];

    function showLoading() {
      loadingElements.forEach(element => {
        document.getElementById(element).style.display = 'block';
      });
    }

    function hideLoading() {
      loadingElements.forEach(element => {
        document.getElementById(element).style.display = 'none';
      });
      document.querySelector('table').style.display = 'table';
    }

    showLoading();

    fetch(`/fetchData/${academicYear}`)
      .then(response => response.json())
      .then(data => {
        const eduroleRegisteredStudents = data.eduroleRegisteredStudents;
        const sisReportsRegisteredStudents = data.sisReportsRegisteredStudents;

        // Update table
        const dataTableBody = document.getElementById('dataTableBody');
        let totalFulltimeRegistrations = eduroleRegisteredStudents.filter(student => student.StudyType === 'Fulltime').length + sisReportsRegisteredStudents.filter(student => student.StudyType === 'Fulltime').length;
        let totalDistanceRegistrations = eduroleRegisteredStudents.filter(student => student.StudyType === 'Distance').length + sisReportsRegisteredStudents.filter(student => student.StudyType === 'Distance').length;

        dataTableBody.innerHTML = `
          <tr>
              <td style="color:#f96332;"><strong>Fulltime</strong></td>
              <td style="color:#f5f5f5;">${eduroleRegisteredStudents.filter(student => student.StudyType === 'Fulltime').length}</td>
              <td style="color:#f5f5f5;">${sisReportsRegisteredStudents.filter(student => student.StudyType === 'Fulltime').length}</td>
              <td style="background-color: #f5f5f5;">${totalFulltimeRegistrations}</td>
          </tr>
          <tr>
              <td style="color:#f96332;"><strong>Distance</strong></td>
              <td style="color:#f5f5f5;">${eduroleRegisteredStudents.filter(student => student.StudyType === 'Distance').length}</td>
              <td style="color:#f5f5f5;">${sisReportsRegisteredStudents.filter(student => student.StudyType === 'Distance').length}</td>
              <td style="background-color: #f5f5f5;">${totalDistanceRegistrations}</td>
          </tr>
          <tr>
              <td style="background-color: #f5f5f5; font-weight: bold;">Total Registered</td>
              <td style="background-color: #f5f5f5;">${eduroleRegisteredStudents.length}</td>
              <td style="background-color: #f5f5f5;">${sisReportsRegisteredStudents.length}</td>
              <td style="background-color: #f5f5f5; font-weight: bold;">${eduroleRegisteredStudents.length + sisReportsRegisteredStudents.length}</td>
          </tr>
        `;

        // Update charts
        updatePieChartExample(eduroleRegisteredStudents, sisReportsRegisteredStudents);
        updateLineChartExampleWithNumbersAndGrid(eduroleRegisteredStudents, sisReportsRegisteredStudents);
        updateBarChartSimpleGradientsNumbers(eduroleRegisteredStudents, sisReportsRegisteredStudents);
        updateBarChartSimpleGradientsNumbersYearOfStudy(eduroleRegisteredStudents, sisReportsRegisteredStudents);
        updateBarChartSimpleGradientsNumbersProgrammes(eduroleRegisteredStudents, sisReportsRegisteredStudents);
        
        hideLoading();
      });

    function updatePieChartExample(edurole, sisReports) {
      // Set better chart options for improved visualization
      const ctx = document.getElementById('pieChartExample').getContext('2d');
      const chart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ['New Edurole', 'Returning Edulrole', 'Returning SISReports', 'New SISReports'],
          datasets: [{
            data: [
              edurole.filter(student => student.StudentType === 'NEWLY ADMITTED').length,
              edurole.filter(student => student.StudentType === 'RETURNING STUDENT').length,
              sisReports.filter(student => student.StudentType === 'RETURNING STUDENT').length,
              sisReports.filter(student => student.StudentType === 'NEWLY ADMITTED').length
            ],
            backgroundColor: [
              'rgba(255, 99, 132, 0.8)',
              'rgba(54, 162, 235, 0.8)',
              'rgba(255, 206, 86, 0.8)',
              'rgba(75, 192, 192, 0.8)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 2,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
              labels: {
                padding: 10,
                font: {
                  size: 12
                },
                usePointStyle: true,
                pointStyle: 'circle'
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 10,
              displayColors: true
            }
          }
        }
      });
      document.getElementById('loadingPieChartExample').style.display = 'none';
    }

    function updateLineChartExampleWithNumbersAndGrid(edurole, sisReports) {
      const ctx = document.getElementById('lineChartExampleWithNumbersAndGrid').getContext('2d');
      const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Fulltime', 'Distance'],
          datasets: [{
            data: [
              edurole.filter(student => student.StudyType === 'Fulltime').length + sisReports.filter(student => student.StudyType === 'Fulltime').length,
              edurole.filter(student => student.StudyType === 'Distance').length + sisReports.filter(student => student.StudyType === 'Distance').length
            ],
            backgroundColor: [
              'rgba(75, 192, 192, 0.8)',
              'rgba(153, 102, 255, 0.8)'
            ],
            borderColor: [
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 2,
            borderRadius: 5,
            hoverOffset: 15
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '60%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 15,
                usePointStyle: true,
                pointStyle: 'rectRounded',
                font: {
                  size: 13,
                  weight: 'bold'
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });
      document.getElementById('loadingLineChartExampleWithNumbersAndGrid').style.display = 'none';
    }

    function updateBarChartSimpleGradientsNumbers(edurole, sisReports) {
      const ctx = document.getElementById('barChartSimpleGradientsNumbers').getContext('2d');
      const totalSchoolsSomcs = edurole.filter(student => student.School === 'SOMCS').length + sisReports.filter(student => student.School === 'SOMCS').length;
      const totalSchoolsSON = edurole.filter(student => student.School === 'SON').length + sisReports.filter(student => student.School === 'SON').length;
      const totalSchoolsSOPHES = edurole.filter(student => student.School === 'SOPHES').length + sisReports.filter(student => student.School === 'SOPHES').length;
      const totalSchoolsDRGS = edurole.filter(student => student.School === 'DRGS').length + sisReports.filter(student => student.School === 'DRGS').length;
      const totalSchoolsIBBS = edurole.filter(student => student.School === 'IBBS').length + sisReports.filter(student => student.School === 'IBBS').length;
      const totalSchoolsSOHS = edurole.filter(student => student.School === 'SOHS').length + sisReports.filter(student => student.School === 'SOHS').length;

      const chart = new Chart(ctx, {
        type: 'polarArea',
        data: {
          labels: ['SOMCS', 'SON', 'SOPHES', 'DRGS', 'IBBS', 'SOHS'],
          datasets: [{
            data: [
              totalSchoolsSomcs,
              totalSchoolsSON,
              totalSchoolsSOPHES,
              totalSchoolsDRGS,
              totalSchoolsIBBS,
              totalSchoolsSOHS
            ],
            backgroundColor: [
              'rgba(255, 99, 132, 0.7)',
              'rgba(255, 159, 64, 0.7)',
              'rgba(255, 205, 86, 0.7)',
              'rgba(75, 192, 192, 0.7)',
              'rgba(54, 162, 235, 0.7)',
              'rgba(153, 102, 255, 0.7)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(255, 159, 64, 1)',
              'rgba(255, 205, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 2,
            borderAlign: 'inner'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            r: {
              ticks: {
                display: false
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.1)'
              }
            }
          },
          plugins: {
            legend: {
              position: 'right',
              labels: {
                font: {
                  size: 11
                },
                usePointStyle: true,
                padding: 10
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });
      document.getElementById('loadingBarChartSimpleGradientsNumbers').style.display = 'none';
    }

    function updateBarChartSimpleGradientsNumbersYearOfStudy(edurole, sisReports) {
      const ctx = document.getElementById('barChartSimpleGradientsNumbersYearOfStudy').getContext('2d');
      const totalEduroleYear1 = edurole.filter(student => student.YearOfStudy === 'YEAR 1').length + sisReports.filter(student => student.YearOfStudy === 'YEAR 1').length;
      const totalEduroleYear2 = edurole.filter(student => student.YearOfStudy === 'YEAR 2').length + sisReports.filter(student => student.YearOfStudy === 'YEAR 2').length;
      const totalEduroleYear3 = edurole.filter(student => student.YearOfStudy === 'YEAR 3').length + sisReports.filter(student => student.YearOfStudy === 'YEAR 3').length;
      const totalEduroleYear4 = edurole.filter(student => student.YearOfStudy === 'YEAR 4').length + sisReports.filter(student => student.YearOfStudy === 'YEAR 4').length;
      const totalEduroleYear5 = edurole.filter(student => student.YearOfStudy === 'YEAR 5').length + sisReports.filter(student => student.YearOfStudy === 'YEAR 5').length;
      const totalEduroleYear6 = edurole.filter(student => student.YearOfStudy === 'YEAR 6').length + sisReports.filter(student => student.YearOfStudy === 'YEAR 6').length;

      const chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['YEAR 1', 'YEAR 2', 'YEAR 3', 'YEAR 4', 'YEAR 5', 'YEAR 6'],
          datasets: [{
            label: 'Students',
            data: [
              totalEduroleYear1,
              totalEduroleYear2,
              totalEduroleYear3,
              totalEduroleYear4,
              totalEduroleYear5,
              totalEduroleYear6
            ],
            backgroundColor: [
              'rgba(255, 99, 132, 0.8)',
              'rgba(255, 159, 64, 0.8)',
              'rgba(255, 205, 86, 0.8)',
              'rgba(75, 192, 192, 0.8)',
              'rgba(54, 162, 235, 0.8)',
              'rgba(153, 102, 255, 0.8)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(255, 159, 64, 1)',
              'rgba(255, 205, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 2,
            borderRadius: 6,
            maxBarThickness: 40
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                precision: 0
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.7)',
              callbacks: {
                label: function(context) {
                  const value = context.raw;
                  const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `Students: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });
      document.getElementById('loadingBarChartSimpleGradientsNumbersYearOfStudy').style.display = 'none';
    }

    function updateBarChartSimpleGradientsNumbersProgrammes(edurole, sisReports) {
      const ctx = document.getElementById('barChartSimpleGradientsNumbersProgrammes').getContext('2d');
      // Use shorter identifiers - program codes only
      const eduroleProgrammeCounts = edurole.reduce((acc, student) => {
        // Use StudyID (short code) instead of full names
        const programmeKey = student.StudyID || student.ProgrammeCode || 'Unknown';
        acc[programmeKey] = (acc[programmeKey] || 0) + 1;
        return acc;
      }, {});
      const sisReportsProgrammeCounts = sisReports.reduce((acc, student) => {
        const programmeKey = student.StudyID || student.ProgrammeCode || 'Unknown';
        acc[programmeKey] = (acc[programmeKey] || 0) + 1;
        return acc;
      }, {});

      const allProgrammeCodes = new Set([...Object.keys(eduroleProgrammeCounts), ...Object.keys(sisReportsProgrammeCounts)]);
      // Sort alphabetically first to group related programmes
      const sortedAlphabetically = Array.from(allProgrammeCodes).sort();

      // Then sort by total count descending within each letter group
      // This creates a more structured, grouped layout
      const sortedProgrammes = sortedAlphabetically.sort((a, b) => {
        // First character should match to keep programs with same prefix together
        if (a.charAt(0) === b.charAt(0)) {
          const totalA = (eduroleProgrammeCounts[a] || 0) + (sisReportsProgrammeCounts[a] || 0);
          const totalB = (eduroleProgrammeCounts[b] || 0) + (sisReportsProgrammeCounts[b] || 0);
          return totalB - totalA;
        }
        return a.localeCompare(b);
      });
      
      // Show all programmes - no limit
      const labels = sortedProgrammes;
      const eduroleData = labels.map(label => eduroleProgrammeCounts[label] || 0);
      const sisReportsData = labels.map(label => sisReportsProgrammeCounts[label] || 0);

      const chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Edurole',
            data: eduroleData,
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            // Remove radius for tighter display
            borderRadius: 0,
            barPercentage: 0.3,
            categoryPercentage: 0.5
          }, {
            label: 'SIS Reports',
            data: sisReportsData,
            backgroundColor: 'rgba(153, 102, 255, 0.8)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1,
            // Remove radius for tighter display
            borderRadius: 0,
            barPercentage: 0.3,
            categoryPercentage: 0.5
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'x',
          // Make the chart more compact
          barThickness: 'flex',
          maxBarThickness: 15,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                usePointStyle: true,
                padding: 10,
                font: {
                  size: 11
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleFont: {
                size: 13
              },
              bodyFont: {
                size: 12
              },
              padding: 10
            }
          },
          scales: {
            y: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 8
                },
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90,
                padding: 0
              }
            },
            x: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                precision: 0
              }
            }
          }
        }
      });
      document.getElementById('loadingBarChartSimpleGradientsNumbersProgrammes').style.display = 'none';
    }
  });
</script>
@endpush
