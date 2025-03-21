@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'User Profile',
    'activePage' => 'users',
    'activeNav' => '',
])

@section('content') 
<div class="panel-header panel-header-sm">
</div>
<div class="content">
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
    <div class="row">
                
        <div class="col-md-5">
            <div class="card card-user">
                <div class="image">
                    <img src="{{ asset('assets/img/bg5.jpg') }}" alt="...">
                </div>
                <div class="card-body">
                
                @if($user->hasRole("Student"))
                    <div class="author">
                    @php

                    $getStudentStatus = \App\Models\Student::where('student_number','=',$user->name)->first();
                    $studentStatus = $getStudentStatus->status;
                    $studentNumbers = $user->name;
                    $academicYear = 2024;

                    $studentInformation = \App\Models\Schools::select(
                            'basic-information.FirstName',
                            'basic-information.MiddleName',
                            'basic-information.Surname',
                            'basic-information.Sex',
                            'student-study-link.StudentID',
                            'basic-information.GovernmentID',
                            'basic-information.PrivateEmail',
                            'basic-information.MobilePhone',
                            'study.ShortName',
                            'study.Name',
                            'schools.Description',
                            'basic-information.StudyType',
                        )
                        ->join('study', 'schools.ID', '=', 'study.ParentID')
                        ->join('student-study-link', 'study.ID', '=', 'student-study-link.StudyID')
                        ->leftJoin('basic-information', 'student-study-link.StudentID', '=', 'basic-information.ID')  
                        ->where('basic-information.StudyType', '!=', 'Staff')
                        ->where('student-study-link.StudentID', $studentNumbers)
                        ->groupBy('student-study-link.StudentID')
                        ->first();

                    //$studentInformation = \App\Models\BasicInformation::where('ID','=',$user->name)->first(); 
                    @endphp
                        <a href="https://edurole.lmmu.ac.zm/information/show/{{ $studentInformation->StudentID }}" target="_blank">
                            <img class="avatar border-gray" src="//edurole.lmmu.ac.zm/datastore/identities/pictures/{{ $studentInformation->StudentID }}.png" 
                                onerror="this.onerror=null;this.src='{{ asset('assets/img/default-avatar.png') }}';" alt="...">
                            <h5 class="title">{{ $studentInformation->FirstName }} {{ $studentInformation->Surname }} </h5>
                        </a>
                        <p>
                            {{ $studentInformation->StudentID }}
                        </p>
                        <p>
                            {{ $studentInformation->GovernmentID }}
                        </p>
                        <p>
                            {{ $studentInformation->PrivateEmail }}
                        </p> 
                        <p>
                            {{ $studentInformation->Name }}
                        </p>
                        <p>
                            {{ $studentInformation->Description }}
                        </p>
                        <p>
                            {{ $studentInformation->StudyType }}
                        </p>                        
                        @if($studentStatus ==  5)
                            <a href="{{route('nmcz.registration',$user->name)}}">
                                <p>
                                    Course Registration NMCZ
                                </p>
                            </a>
                            <a href="{{route('docket.showStudentNmcz',$user->name)}}">
                                <p>
                                    Examination Docket
                                </p>
                            </a>
                        @else                       
                            <a href="{{route('students.showStudent',$user->name)}}">
                                <p>
                                    Course Registration LMMU
                                </p>  
                            </a>  

                            <a href="{{url('/student/viewSupplementaryDocket/' .$user->name)}}"> 
                            {{-- <a href="{{route('docket.showStudent',$user->name)}}"> --}}
                                <p>
                                    Examination Docket
                                </p>   
                            </a>                    
                        @endif
                            
                    </div>
                @else
                    <div class="author">
                        <a href="#">
                            <img class="avatar border-gray" src="{{ asset('assets/img/default-avatar.png') }}" alt="...">
                            
                            <h5 class="title">{{ $user->name }}</h5>
                        </a>
                        <h6 class="description">
                            {{ $user->email }}
                        </h6>                            
                    </div>

                @endif 
                </div>           
            </div>
        </div>
        <div class="col-md-7">
            <div class="row">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Update User </h4>
                    
                </div>
                <div class="card-body">
                    <div class="toolbar">
                        <!-- Here you can write extra buttons/actions for the toolbar -->
                    </div>
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @method('patch')
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input value="{{ $user->name }}" type="text" class="form-control" name="name" placeholder="Name" required>

                            @if ($errors->has('name'))
                            <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        @if(!$user->hasRole("Student"))
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input value="{{ $user->email }}" type="email" class="form-control" name="email" placeholder="Email address" required>
                            @if ($errors->has('email'))
                            <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        @endif
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" name="role" required>
                                <option value="">Select role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                            @if ($errors->has('role'))
                            <span class="text-danger text-left">{{ $errors->first('role') }}</span>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">Update user</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>

                    </form>
                </div>
                <!-- end card-body -->
            </div>
            <!-- end card -->       
            </div>
            <div class="row">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update Password</h4>
                    </div>
                    <div class="card-body">
                        <div class="toolbar">
                            <!-- Here you can write extra buttons/actions for the toolbar -->
                        </div>
                        <form method="POST" action="{{ route('users.resetUserPassword', $user->id) }}">
                            @csrf
                            <button type="submit" rel="tooltip" class="btn btn-info" data-original-title="" title="">
                                Reset Password
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.resetPassword', $user->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                @error('password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update Password</button>
                            <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                    <!-- end card-body -->
                </div>
            </div>
            <!-- end card -->
            </div>
        <!-- end col-md-6 -->
        </div>
    <!-- end row -->
</div>
@endsection
