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
                @if($studentStatus != 5)
                    <h4 class="card-title">Registered Courses</h4>
                @else
                    <h4 class="card-title">Registered NMCZ Courses</h4>
                @endif
            </div>
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
                </div>
            <div class="toolbar">
                
                
                
            <div class="row">
                <div class="col-md-6">
                    @if ((auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
                    <form method="POST" action="{{ route('deleteEntireRegistration.student') }}" class="ml-3">
                        @csrf
                        @method('DELETE')
                        
                        <input type="hidden" name="studentId" value="{{ $studentId }}">
                        <input type="hidden" name="year" value="2025">
                        <button type="submit" class="btn btn-danger">Delete Registration</button>
                    </form>  
                    @endif            
                </div>   
                <div class="col-md-6 text-right">
                    @if ((auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
                        <a href="{{ route('printIDCard.student', ['studentId' => $studentId]) }}" class="mr-3">
                            <button class="btn btn-info">PRINT ID CARD</button>
                        </a>
                        {{-- @if($studentStatus != 7) --}}
                            {{-- @if(($actualBalance <= 0) || ($failed == 1))
                                <a href="{{ route('printIDCard.student', ['studentId' => $studentId]) }}" class="mr-3">
                                    <button class="btn btn-info">PRINT ID CARD</button>
                                </a>
                            @else
                                <p>Student has a balance of K{{ $actualBalance }}</p>
                            @endif --}}
                        {{-- @else --}}
                            {{-- @if(($actualBalance <= 0) || ($failed == 1)) --}}
                                {{-- <a href="{{ route('printIDCard.studentNurandMid', ['studentId' => $studentId]) }}" class="mr-3">
                                    <button class="btn btn-info">PRINT ID CARD</button>
                                </a> --}}
                            {{-- @else --}}
                                {{-- <p>Student has a balance of K{{ $actualBalance }}</p> --}}
                            {{-- @endif --}}
                        {{-- @endif --}}
                    @endif            
                </div>              
            </div>       
        </div>           
                <div class="card-body"> 
                    <div class="card">  
                        <div class="card-header">
                            <h6 class="card-title">Student Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p> First Name : {{ $studentInformation->FirstName }}</p>
                                    <p> Last Name : {{ $studentInformation->Surname }}</p>
                                    <p> Student Number : {{ $studentInformation->StudentID }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p>Programme : {{ $studentInformation->Name }}</p>
                                    <p>Mode Of Stduy : {{ $studentInformation->StudyType }}</p>
                                    <p>Balance : {{ $studentInformation->Amount }}</p>
                                </div>
                            </div>
                        </div>
                        
                    </div>                 
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Course Code</th>
                                    @if($studentStatus != 5)
                                    <th>Course Name</th>
                                    @else
                                    <th>Course Year</th>
                                    @endif
                                    @if ((auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
                                    <th class="text-right">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($checkRegistration as $result)
                            <tr>
                            @if($studentStatus != 5)
                                <td>{{$result->Name}}</td>
                                <td>{{ $result->CourseDescription }}  {{ $result->Year }}
                                <td class="text-right">
                                    @if ((auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
                                    <form method="POST" action="{{ route('deleteCourseInRegistration.student') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="courseId" value="{{ $result->Name }}">
                                        <input type="hidden" name="studentStatus" value="{{ $studentStatus }}">
                                        <input type="hidden" name="studentId" value="{{ $studentId }}">
                                        <input type="hidden" name="year" value="2024">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    @endif
                                </td>
                            @else
                                <td>{{$result->CourseID}}</td>
                                <td>{{ $result->Year }}
                                <td class="text-right">
                                    @if ((auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
                                    <form method="POST" action="{{ route('deleteCourseInRegistration.student') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="courseId" value="{{ $result->CourseID }}">
                                        <input type="hidden" name="studentStatus" value="{{ $studentStatus }}">                                        
                                        <input type="hidden" name="studentId" value="{{ $studentId }}">
                                        <input type="hidden" name="year" value="2024">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                    @endif
                                </td>
                            @endif
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