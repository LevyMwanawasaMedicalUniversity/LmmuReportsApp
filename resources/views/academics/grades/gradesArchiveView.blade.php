@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'View Grades Archive',
    'activePage' => 'viewGradesArchive',
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
                    
                </div>
                
                @if(!empty($results))
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                            <tr>
                                <th>First Name</th>
                                <th>Surname</th>
                                <th>Student Number</th>
                                <th>Email</th>
                                <th>Programme</th>
                                <th>Study Mode</th>
                                <th>Year Of Study</th>
                                <th class="text-end">Action</th>
                                <!-- Add other table headers for additional properties -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                            <tr>
                                <td>{{$result->FirstName}}</td>
                                <td>{{$result->Surname}}</td>
                                <td>{{$result->StudentID}}</td>
                                <td>{{$result->PrivateEmail}}</td>
                                <td>{{$result->Name}}</td>
                                <td>{{$result->StudyType}}</td>
                                <td>{{$result->YearOfStudy}}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                        
                                        <a href="{{route('archivedResults.showStudent',$result->StudentID)}}" class="btn btn-primary">View</a>
                                    </div>
                                </td>
                                <!-- Add other table cells for additional properties -->
                            </tr>
                            
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    
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