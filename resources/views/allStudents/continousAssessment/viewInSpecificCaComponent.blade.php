@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Continous Assessment',
    'activePage' => 'docket-index',
    'activeNav' => '',
])

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Continous Assessment Components</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                        <tr>
                            <th>#</th>
                            <th>Assessment Number</th>
                            <th>Description</th> 
                            <th class="text-end">Percentage Score</th>   
                        </tr>
                        </thead>
                        <tbody> 
                            @foreach($results as $result)
                            @php
                                

                            @endphp
                                <tr>
                                    
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$result->assesment_type_name}} {{$loop->iteration}}</td>
                                    <td>{{$result->description}}</td>
                                    <td class="text-end">{{$result->cas_score}} %</td>                                    

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