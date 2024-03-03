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
            <h4 class="card-title">Course Registration</h4>
          </div>
          <div class="toolbar">
              
           
          
            <div class="row align-items-center">
                <div class="col-md-5 ml-3">
                                        
                </div>                
            </div>        
            </div>           
                <div class="card-body">                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                            <tr>
                                <th>Course Code</th>                                
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($checkRegistration as $result)
                            <tr>
                                <td>{{$result->CourseID}}</td>                                                           
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

@endsection