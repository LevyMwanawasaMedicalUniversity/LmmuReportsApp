@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Academics Queries',
    'activePage' => 'registrationCheck',
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
                    <form action="{{ route('academics.registrationCheck') }}" method="GET">
                        @csrf
                        
                        <div class="form-group">
                            <label for="search">Search Students</label>
                            <input type="number" name="student-number" class="form-control" id="student-number" placeholder="Enter student name or ID">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
@endsection