@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'User Profile',
    'activePage' => 'roles',
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
              <a class="btn btn-primary btn-round text-white pull-right" href="{{ route('roles.create') }}">Add Role</a>
            <h4 class="card-title">Roles</h4>
            <div class="col-12 mt-2">
                                        </div>
          </div>
          <div class="card-body">
            <div class="toolbar">
              <!--        Here you can write extra buttons/actions for the toolbar              -->
            </div>
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                    <th>Name</th>
                    <th class="disabled-sorting text-right">Actions</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                    <th>Name</th>
                    <th class="disabled-sorting text-right">Actions</th>
                </tr>
              </tfoot>
              @if(count($roles ) > 0)
              <tbody>
                 @foreach ($roles as $key => $role)
                  <tr>
                    <td>{{ $role->name }}</td>
                      <td class="text-right">
                        <a type="button" href="{{ route('roles.show', $role->id) }}" rel="tooltip" class="btn btn-success btn-icon btn-sm " data-original-title="" title="">
                            <i class="now-ui-icons business_bulb-63"></i>
                        </a>
                        <a type="button" href="{{ route('roles.edit', $role->id) }}" rel="tooltip" class="btn btn-success btn-icon btn-sm " data-original-title="" title="">
                            <i class="now-ui-icons ui-2_settings-90"></i>
                        </a>
                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display: inline">
                            @csrf
                            @method('DELETE')
                             <div class="form-group mb-3 text-center row mt-3 pt-1">
                                <div class="col-12">
                            <button class="btn btn-link btn-sm text-danger" type="submit" onclick="return confirm('Are you sure you want to delete this role?')">
                                <i class="ri ri-delete-bin-fill" style="margin-left: 3px; margin-right: 3px;"></i>
                                <span title="Delete Role" class="ri ri-delete-bin-fill" style="margin-left: 3px; margin-right: 3px;"></span>
                            </button>
                             </div>
                            </div> 
                        </form> 
                    </td>
                  </tr>
                @endforeach
              </tbody>
              @else
              <tbody>
                <tr>
                    <h3 class="text-center">No Roles.</h3>
                </tr>
              </tbody>
              @endif
            </div>
            </table>
          </div>
          <!-- end content-->
          
        </div>
        <!--  end card  -->
      </div>
      <!-- end col-md-12 -->
    </div>
    
    <!-- end row -->
  </div>
  @endsection