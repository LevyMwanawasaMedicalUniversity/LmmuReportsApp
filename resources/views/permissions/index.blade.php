@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'User Profile',
    'activePage' => 'permissions',
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
              <a class="btn btn-primary btn-round text-white pull-right" href="{{ route('permissions.create') }}">Add Permission</a>
            <h4 class="card-title">Permissions</h4>
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
                    <th>Auth Guard</th>
                    <th class="disabled-sorting text-right">Actions</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Auth Guard</th>
                    <th class="disabled-sorting text-right">Actions</th>
                </tr>
              </tfoot>
              @if(count($permissions) > 0)
              <tbody>
                @foreach($permissions as $permission)
                  <tr>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->guard_name }}</td>
                      <td class="text-right">
                        <a type="button" href="{{ route('permissions.edit', $permission->id) }}" rel="tooltip" class="btn btn-success btn-icon btn-sm " data-original-title="" title="">
                            <i class="now-ui-icons ui-2_settings-90"></i>
                        </a>
                        <form method="POST" action="{{ route('permissions.destroy', $permission->id) }}" style="display: inline">
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
                
                    <h3 class="text-center">No Permissions.</h3>
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