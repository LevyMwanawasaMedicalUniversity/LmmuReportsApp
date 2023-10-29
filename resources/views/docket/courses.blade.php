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
                    <form action="{{ route('courses.import') }}" method="GET">
                        @csrf
                        
                        <div class="form-group">
                            <label for="search">Search Courses</label>
                            <input type="text" name="course-code" class="form-control" id="course-code" placeholder="Enter cousrse code">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Search</button>
                      </form>
                </div>
                <div class="card-body">
                
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th class="text-end">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($courses as $result)
                            <tr>
                                <td>{{$result->course_code}}</td>
                                <td>{{$result->course_name}}</td>
                                <td class="text-end"><a href="{{ route('courses.examlist',$result->id) }}">View</a></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- {{ $courses->links('pagination::bootstrap-4') }} --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection