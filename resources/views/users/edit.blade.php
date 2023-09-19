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
            <h4 class="card-title">Update User </h4>
            <div class="col-12 mt-2">
            </div>
          </div>
          <div class="card-body">
            <div class="toolbar">
              <!--        Here you can write extra buttons/actions for the toolbar              -->
            </div>
            <form method="POST" action="{{ route('users.update', $user->id) }}">
              @method('patch')
              @csrf
              <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input value="{{ $user->name }}" 
                      type="text" 
                      class="form-control" 
                      name="name" 
                      placeholder="Name" required>

                  @if ($errors->has('name'))
                      <span class="text-danger text-left">{{ $errors->first('name') }}</span>
                  @endif
              </div>
              <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input value="{{ $user->email }}"
                      type="email" 
                      class="form-control" 
                      name="email" 
                      placeholder="Email address" required>
                  @if ($errors->has('email'))
                      <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                  @endif
              </div>
              <div class="mb-3">
                  <label for="role" class="form-label">Role</label>
                  <select class="form-control" name="role" required>
                      <option value="">Select role</option>
                      @foreach($roles as $role)
                          <option value="{{ $role->id }}"
                              {{ $user->hasRole($role->name) ? 'selected' : '' }}>
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
          <!-- end content-->
        </div>
        <!--  end card  -->
      </div>
      <!-- end col-md-12 -->
    </div>
    <!-- end row -->

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">              
            <h4 class="card-title">Update Password</h4>
            <div class="col-12 mt-2">
                                        </div>
          </div>
          <div class="card-body">
            <div class="toolbar">
              <!--        Here you can write extra buttons/actions for the toolbar              -->
            </div>
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
          <!-- end content-->
        </div>
        <!--  end card  -->
      </div>
      <!-- end col-md-12 -->
    </div>
  </div>
  @endsection
    