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
           
          {{-- <form action="{{ route('viewAllProgrammesPerSchool') }}" method="GET">
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
        </form> --}}
        </div>
            @if(!empty($results))
            
            <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            {{-- <th>Programme Name</th>
                            <th>Programme Code</th> --}}
                            <th>School Name</th>
                            <th>Delivery Mode</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($results as $result)
                        <tr>
                            {{-- <td>{{$result->ID}}</td>
                            <td>{{$result->Account}}</td> --}}
                            <td>{{$result->Credit}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $results->appends(['schoolName' => $schoolName])->links('pagination::bootstrap-4') }}
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