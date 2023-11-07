@extends('layouts.app', [
    'namePage' => 'Dashboard',
    'class' => 'sidebar-mini ',
    'activePage' => 'home',
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
                <centre><h4 class="card-title">Welcome To The Reports Dashboard</h4></centre>
              </div>
              <div class="card-body">
                <img src="{{ asset('assets') }}/img/DJI_0097.JPG" alt="LMMU">
                {{-- <img src="{{ asset('images/DJI_0097.jpg') }}" alt="Description of your image"> --}}
              </div>
            </div>
          </div>
      </div>
    </div> 
@endsection

