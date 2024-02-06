@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Finance Queries',
    'activePage' => 'invoicesPage',
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
        <div class="row">
            <div class="col-md-6">
                @if(!empty($results))
                <a class="btn btn-success float-right mt-3 mr-2" href="{{ route('finance.ExportAllProgrammeInvoices') }}">Export Data</a>
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
                            <th>InvNumber</th>
                            <th>Description</th>
                            <th>Invoice Year</th>
                            <th>Study Mode</th>
                            <th>Year Of Study</th>
                            <th>Programme Code</th>
                            <th>InvDate</th>
                            <th>Amount</th>                         
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{ $result->InvNumber }}</td>
                            <td>{{ $result->InvoiceDescription }}</td>
                            <td>{{ $result->InvoiceYearOfInvoice }}</td>
                            <td>{{ $result->InvoiceModeOfStudy }}</td>
                            <td>{{ $result->InvoiceYearOfStudy }}</td>
                            <td>{{ $result->InvoiceProgrammeCode }}</td> 
                            <td>{{ $result->InvDate }}</td>
                            <td>K {{ $result->InvoiceAmount }}</td>                                                    
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