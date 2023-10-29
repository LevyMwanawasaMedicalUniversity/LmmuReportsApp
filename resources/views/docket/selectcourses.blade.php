@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Docket',
    'activePage' => 'docket-courses',
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
                    <h4 class="card-title">Course</h4>
                    <br>
                    <h6>Search (Ctrl + F) for and select required course(s), then click "Enter" or the "Save Changes" button at the bottom of the table to submit</h6>
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
                <div class="card-body">
                
                    <div class="table-responsive">
                        <form method="POST" action="{{route('courses.store',$studentId)}}">
                    
                            @csrf  
                            <table class="table">
                                <thead class="text-primary">
                                <tr>
                                    <th>Select</th>
                                    <th>Course Code</th>
                                    <th class="text-end">Course Name</th>
                                    
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($courses as $result)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="course[]" value="{{$result->id}}" class="subject">

                                    </td>
                                    <td>{{$result->course_code}}</td>
                                    <td class="text-end">{{$result->course_name}}</td>
                                    
                                    
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection