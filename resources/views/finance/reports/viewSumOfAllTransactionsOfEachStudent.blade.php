@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Finance Queries',
    'activePage' => 'finance',
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
            <h4 class="card-title"> Finance Queries</h4>
          </div>
          <div class="toolbar">
              <!--        Here you can write extra buttons/actions for the toolbar              -->
           
          <!-- {{-- <form action="{{ route('viewAllProgrammesPerSchool') }}" method="GET">
            <div class="row">
                <div class="col-md-5 ml-3">
                    <div class="form-group">
                        <label for="schoolName"><h6><b>Select School:</b></h6></label>
                        <select name="schoolName" id="schoolName" class="form-control">
                            <option value="">NONE</option>
                            <option value="SOHS" {{ $schoolName == 'SOHS' ? 'selected' : '' }}>School of Health Sciences</option>
                            <option value="SOMCS" {{ $schoolName == 'SOMCS' ? 'selected' : '' }}>School of Medicine and Clinical Sciences</option>
                            <option value="SOPHES" {{ $schoolName == 'SOPHES' ? 'selected' : '' }}>School of Public Health and Environmental Sciences</option>
                            <option value="IBBS" {{ $schoolName == 'IBBS' ? 'selected' : '' }}>Institute of Basic and Biomedical Sciences</option>
                            <option value="SON" {{ $schoolName == 'SON' ? 'selected' : '' }}>School Of Nursing</option>
                            <option value="DRPGS" {{ $schoolName == 'DRPGS' ? 'selected' : '' }}>Directorate Of Research And Graduate Studies</option>
                        </select>
                    </div>
                </div>
                
            </div>
            <div class="row align-items-center">
                <div class="col-md-5 ml-3">
                    <button class="btn btn-primary -mt3" type="submit">
                        View Results
                    </button>
                </div>
                <div class="col-md-6">
                    @if(!empty($results))
                    <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('exportAllProgrammesPerSchool',$schoolName) }}">Export Data</a>
                    @endif
                </div>
            </div>
        </form> --}} -->
        <div class="row">
            <div class="col-md-6">
                @if(!empty($results))
                <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('exportAllPaymentInformation') }}">Export Data</a>
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
                            <th>First Name</th>
                            <th>Surname</th>
                            <th>Sex</th>
                            <th>Student ID</th>
                            <th>Government ID</th>
                            <th>Programme Code</th>
                            <th>Study Name</th>
                            <th>School</th>
                            <th>Study Type</th>
                            <th>Registration Status</th>
                            <th>Year Of Study</th>                            
                            <th>Total Payments</th>
                            <th>Total Payments Before 2023</th>
                            <th>Total Payments 2023</th>
                            <th>Total Payments 2024</th>
                            <th>2023 Invoice Status</th>
                            <th>2024 Invoice Status</th>
                            <th>Latest Invoice Date</th>                         
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{ $result['FirstName'] }}</td>
                            <td>{{ $result['Surname'] }}</td>
                            <td>{{ $result['Sex'] }}</td>
                            <td>{{ $result['StudentID'] }}</td>
                            <td>{{ $result['GovernmentID'] }}</td>
                            <td>{{ $result['ProgrammeCode'] }}</td>
                            <td>{{ $result['StudyName'] }}</td>
                            <td>{{ $result['School'] }}</td>
                            <td>{{ $result['StudyType'] }}</td>
                            <td>{{ $result['RegistrationStatus'] }}</td>
                            <td>{{ $result['YearOfStudy'] }}</td>
                            <td>{{ isset($result['TotalPayments']) ? $result['TotalPayments'] : '' }}</td>
                            <td>{{ isset($result['TotalPaymentBefore2023']) ? $result['TotalPaymentBefore2023'] : '' }}</td>                            
                            <td>{{ isset($result['TotalPayment2023']) ? $result['TotalPayment2023'] : '' }}</td>
                            <td>{{ isset($result['TotalPayment2024']) ? $result['TotalPayment2024'] : '' }}</td>
                            <td>{{ isset($result['2023InvoiceStatus']) ? $result['2023InvoiceStatus'] : '' }}</td>
                            <td>{{ isset($result['2024InvoiceStatus']) ? $result['2024InvoiceStatus'] : '' }}</td>
                            <td>{{ isset($result['LatestInvoiceDate']) ? $result['LatestInvoiceDate'] : '' }}</td>    
                                                    
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $results->links('pagination::bootstrap-4') }}
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

@endsection