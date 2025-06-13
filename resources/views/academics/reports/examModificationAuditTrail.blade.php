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
                            <input type="text" name="filterInput" id="filterInput"class="form-control" placeholder="Filter by student number, course code, date...">
                        </div>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-success float-right mt-3 mr-2" id="exportToExcel">Export to Excel</button>
                    
                </div>
            </div>
        
        </div>
            
            <!-- Loading indicator -->
            <div id="loadingIndicator" class="text-center my-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3">Loading audit trail data...</p>
            </div>
            
            <div class="card-body" id="tableContainer" style="display: none;">
                
                <div class="table-responsive">
                    <table class="table" id="auditTrailTable">
                        <thead class="text-primary">
                        <tr>
                            <th>Student Number</th>
                            <th>Course Code</th>
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
                        <tbody id="auditTrailTableBody">
                        @if(!empty($results))
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
                        @endif
                        </tbody>
                    </table>
                </div>
                
            </div>
            
            <div id="noResults" class="card-body text-center" style="display: none;">
                <h3>No data found. Please try again later.</h3>
            </div>
        </div>
      </div>      
    </div>
  </div>

@push('js')
<script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const loadingIndicator = document.getElementById('loadingIndicator');
        const tableContainer = document.getElementById('tableContainer');
        const noResults = document.getElementById('noResults');
        const filterInput = document.getElementById('filterInput');
        const table = document.getElementById('auditTrailTable');
        const exportButton = document.getElementById('exportToExcel');
        
        // Check if there's data in the table and update UI accordingly
        setTimeout(() => {
            const tableRows = document.querySelectorAll('#auditTrailTableBody tr');
            
            if (tableRows.length > 0) {
                loadingIndicator.style.display = 'none';
                tableContainer.style.display = 'block';
            } else {
                loadingIndicator.style.display = 'none';
                noResults.style.display = 'block';
            }
        }, 1000); // Small delay for better UX
        
        // Filter functionality
        filterInput.addEventListener('input', function () {
            const filter = filterInput.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                const studentNumber = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const courseCode = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const submittedBy = row.querySelector('td:nth-child(7)').textContent.toLowerCase();
                const reviewedBy = row.querySelector('td:nth-child(8)').textContent.toLowerCase();
                const changeType = row.querySelector('td:nth-child(10)').textContent.toLowerCase();
                const date = row.querySelector('td:nth-child(11)').textContent.toLowerCase();
                
                if (
                    studentNumber.includes(filter) || 
                    courseCode.includes(filter) || 
                    submittedBy.includes(filter) || 
                    reviewedBy.includes(filter) || 
                    changeType.includes(filter) || 
                    date.includes(filter)
                ) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Export to Excel functionality
        exportButton.addEventListener('click', function() {
            exportTableToExcel();
        });
        
        function exportTableToExcel() {
            // Create a workbook
            const wb = XLSX.utils.book_new();
            
            // Get all visible rows (not filtered out)
            const visibleRows = Array.from(document.querySelectorAll('#auditTrailTableBody tr')).filter(
                row => row.style.display !== 'none'
            );
            
            // Get header cells
            const headerCells = Array.from(document.querySelectorAll('#auditTrailTable thead th')).map(
                th => th.textContent.trim()
            );
            
            // Convert table data to worksheet format
            const wsData = [headerCells];
            
            visibleRows.forEach(row => {
                const rowData = Array.from(row.querySelectorAll('td')).map(
                    cell => cell.textContent.trim()
                );
                wsData.push(rowData);
            });
            
            // Create worksheet and add to workbook
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            XLSX.utils.book_append_sheet(wb, ws, "Audit Trail");
            
            // Generate filename with current date
            const now = new Date();
            const dateStr = now.toISOString().slice(0, 10);
            const filename = `exam_modification_audit_trail_${dateStr}.xlsx`;
            
            // Save the workbook as an Excel file
            XLSX.writeFile(wb, filename);
        }
    });
</script>
@endpush

@endsection