@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Docket',
    'activePage' => 'docket-import',
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
                    <h4 class="card-title">Import Student</h4>
                    <div class="col-md-12">
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

                        @if (session('warning'))
                            <div class="alert alert-warning">
                                {{ session('warning') }}
                            </div>
                        @endif
                    </div>
                </div> 
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-4">
                            <div class="card">  
                                <div class="card-header">
                                    <h4 class="card-title">Upload Single Student</h4>
                                </div> 
                                <div class="card-body">                
                                    <form action="{{ route('students.uploadSingleStudent') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="studentId">Student ID</label>
                                            <div class="col-md-8">
                                                <input type="number" name="studentId" class="form-control" id="studentId">
                                            </div>                            
                                        </div>
                                        <button type="submit" class="btn btn-primary">IMPORT</button>
                                    </form>
                                </div>
                            </div>
                        </div>                
                        <div class="col-md-4">
                            <div class="card">  
                                <div class="card-header">
                                    <h4 class="card-title">BULK UPLOAD STUDENTS</h4>
                                </div> 
                                <div class="card-body">
                                    <form action="{{ route('students.import') }}" method="POST" onsubmit="return confirm('Are you sure you want to import students from Edurole?')">
                                        @csrf
                                        <div class="form-group">
                                            <label for="studentId">Bulk Import</label>
                                            <div class="col-md-12">
                                                <p>CLICK THE BUTTON BELOW TO IMPORT STUDENTS FROM EDUROLE</p>
                                            </div>                            
                                        </div>
                                        <button type="submit" class="btn btn-warning">BULK IMPORT</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">  
                                <div class="card-header">
                                    <h4 class="card-title">BULK IMPORT TO MOODLE</h4>
                                </div> 
                                <div class="card-body">
                                    <form action="{{ route('bulkEnrollOnMooodle') }}" method="POST" onsubmit="return confirm('Are you sure you want to add registered students to moodle?')">
                                        @csrf
                                        <div class="form-group">
                                            <label for="studentId">Moodle Import</label>
                                            <div class="col-md-12">
                                                <p>CLICK THE BUTTON ENROLL REGISTERED STUDENTS ON MOODLE</p>
                                            </div>                            
                                        </div>
                                        <button type="submit" class="btn btn-warning">BULK IMPORT</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection