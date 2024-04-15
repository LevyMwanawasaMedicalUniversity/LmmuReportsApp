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
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            
              <a class="btn btn-primary btn-round text-white pull-right" href="{{ route('users.create') }}">Add user</a>
            <h4 class="card-title">Users</h4>
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
                    <form action="{{ route('users.index') }}" method="GET">
                      @csrf
                      
                      <div class="form-group">
                          <label for="search">Search Users</label>
                          <input type="text" name="name" class="form-control" id="name" placeholder="Enter student number or user name">
                      </div>
                      
                      <button type="submit" class="btn btn-primary">Search</button>
                    </form>
          </div>
          <div class="card-body">
            <div class="toolbar">
              <!--        Here you can write extra buttons/actions for the toolbar              -->
            </div>
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>Profile</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Creation date</th>
                  <th class="disabled-sorting text-right">Actions</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>Profile</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Creation date</th>
                  <th class="disabled-sorting text-right">Actions</th>
                </tr>
              </tfoot>
              <tbody>
                @foreach ($users as $user)
                  <tr>
                    <td>
                      <span class="avatar avatar-sm rounded-circle">
                        <img src="{{asset('assets')}}/img/default-avatar.png" alt="" style="max-width: 80px; border-radius: 100px">
                      </span>
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{$user->created_at}}</td>
                    <td class="text-right">
                    <form method="POST" action="{{ route('users.resetUserPassword', $user->id) }}">
                        @csrf
                        <button type="submit" rel="tooltip" class="btn btn-info btn-icon btn-sm " data-original-title="" title="">
                            <i class="now-ui-icons ui-1_lock-circle-open"></i>
                        </button>
                    </form>
                      </td>
                      <td class="text-right">
                          <a type="button" href="{{ route('users.edit', $user->id) }}" rel="tooltip" class="btn btn-success btn-icon btn-sm " data-original-title="" title="">
                        <i class="now-ui-icons ui-2_settings-90"></i>
                        </a>
                      </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- end content-->
          {!! $users->links('pagination::bootstrap-4') !!}
        </div>
        <!--  end card  -->
      </div>
      <!-- end col-md-12 -->
    </div>
    
    <!-- end row -->
  </div>
  @endsection