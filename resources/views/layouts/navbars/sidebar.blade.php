<div class="sidebar" data-color="red">
  <!--
    Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
-->
  <div class="logo">
    <a href="{{ route('home') }}" sizes="76x76" class="simple-text logo-mini">
    <!-- <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png"> -->
      <img src="{{ asset('assets') }}/img/logo2.png" alt="Logo">
      {{ asset('assets') }}/css/bootstrap.min.css
    </a>
    <a href="{{ route('home') }}" class="simple-text logo-normal">
      {{ __('LMMU REPORTS') }}
    </a>
  </div>
  <div class="sidebar-wrapper" id="sidebar-wrapper">
    <ul class="nav">
      <li class="@if ($activePage == 'home') active @endif">
        <a href="{{ route('home') }}">
          <i class="now-ui-icons design_app"></i>
          <p>{{ __('Dashboard') }}</p>
        </a>
      </li>
      @if ((auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')) )
        <li>
          <a data-toggle="collapse" href="#administrationExamples">
            <i class="now-ui-icons objects_globe"></i>
            <p>
              {{ __("Administration") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="administrationExamples">
            <ul class="nav">
              <li class="@if ($activePage == 'users') active @endif">
                <a href="{{ route('users.index') }}">
                  <i class="now-ui-icons users_single-02"></i>
                  <p> {{ __("User Management") }} </p>
                </a>
              </li>
              <li class="@if ($activePage == 'roles') active @endif">
                <a href="{{ url('roles') }}">
                  <i class="now-ui-icons business_badge"></i>
                  <p> {{ __("Roles") }} </p>
                </a>
              </li>
              <li class="@if ($activePage == 'permissions') active @endif">
                <a href="{{ url('permissions') }}">
                  <i class="now-ui-icons objects_key-25"></i>
                  <p> {{ __("Permissions") }} </p>
                </a>
              </li>
            </ul>
          </div>
        </li>
      @endif
      @if (auth()->user()->hasRole('Developer'))
        <li>
          <a data-toggle="collapse" href="#devToolsExamples">
            <i class="fab fa-laravel"></i>
            <p>
              {{ __("Dev Tools") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="devToolsExamples">
            <ul class="nav">              
              <li class="@if ($activePage == 'notifications') active @endif">
                <a href="{{ route('page.index','notifications') }}">
                  <i class="now-ui-icons ui-1_bell-53"></i>
                  <p>{{ __('Notifications') }}</p>
                </a>
              </li>
              <li class="@if ($activePage == 'icons') active @endif">
                <a href="{{ route('page.index','icons') }}">
                  <i class="now-ui-icons education_atom"></i>
                  <p>{{ __('Icons') }}</p>
                </a>
              </li>
              <li class="@if ($activePage == 'table') active @endif">
                <a href="{{ route('page.index','table') }}">
                  <i class="now-ui-icons design_bullet-list-67"></i>
                  <p>{{ __('Table List') }}</p>
                </a>
              </li>
              <li class="@if ($activePage == 'typography') active @endif">
                <a href="{{ route('page.index','typography') }}">
                  <i class="now-ui-icons text_caps-small"></i>
                  <p>{{ __('Typography') }}</p>
                </a>
              </li>         
            </ul>
          </div>
        </li>
      @endif
      @if ((auth()->user()->hasRole('Academics')) || (auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
        <li>
          <a data-toggle="collapse" href="#academics">
            <i class="now-ui-icons education_hat"></i>
            <p>
              {{ __("Academics") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="academics">
            <ul class="nav">   
                        
              <li class = "@if ($activePage == 'academics') active @endif">
                <a href="{{ route('academics.index') }}">
                  <i class="now-ui-icons files_single-copy-04"></i>
                  <p>{{ __('Reports') }}</p>
                </a>
              </li>
              <li class = "@if ($activePage == 'registrationCheck') active @endif">
                <a href="{{ route('academics.registrationCheck') }}">
                  <i class="now-ui-icons education_glasses"></i>
                  <p>{{ __('Registration Check') }}</p>
                </a>
              </li> 
              <li class="@if ($activePage == 'importGradesArchive') active @endif">
                <a href="{{ route('academics.GradesArchiveImport') }}">
                  <i class="now-ui-icons arrows-1_share-66"></i>
                  <p>{{ __('Import to Grades Archive') }}</p>
                </a>
              </li>    
              <li class="@if ($activePage == 'viewGradesArchive') active @endif">
                <a href="{{ route('academics.GradesArchiveView') }}">
                  <i class="now-ui-icons business_briefcase-24"></i>
                  <p>{{ __('Grades Archive') }}</p>
                </a>
              </li>                    
            </ul>
          </div>
        </li>
      @endif
      @if (auth()->user()->hasRole('Finance') || (auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
      <li>
          <a data-toggle="collapse" href="#finance">
            <i class="now-ui-icons business_bank"></i>
            <p>
              {{ __("Finance") }}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse" id="finance">
            <ul class="nav">              
              <li class = "@if ($activePage == 'finance') active @endif">
                <a href="{{ route('finance.index') }}">
                  <i class="now-ui-icons files_single-copy-04"></i>
                  <p>{{ __('Reports') }}</p>
                </a>
              </li>
              <li class="@if ($activePage == 'invoicesPage') active @endif">
                <a href="{{ route('finance.ViewInvoicesPerProgramme') }}">
                  <i class="now-ui-icons arrows-1_share-66"></i>
                  <p>{{ __('Invoices in Programmes') }}</p>
                </a>
              </li>                                  
            </ul>
          </div>
        </li>
      @endif
      @if (auth()->user()->hasRole('Examination') || (auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Developer')))
      <li>
        <a data-toggle="collapse" href="#docketExamples">
          <i class="now-ui-icons education_paper"></i>
          <p>
            {{ __("Docket") }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="docketExamples">
          <ul class="nav">
            <li class="@if ($activePage == 'docket-import') active @endif">
              <a href="{{ route('docket.import') }}">
                <i class="now-ui-icons arrows-1_share-66"></i>
                <p> {{ __("Import Students") }} </p>
              </a>
            </li>
            <li class="@if ($activePage == 'docket-index') active @endif">
              <a href="{{ route('docket.index') }}">
                <i class="now-ui-icons text_align-left"></i>
                <p> {{ __("View Students") }} </p>
              </a>
            </li>
            <li class="@if ($activePage == 'docket-courses') active @endif">
              <a href="{{ route('courses.import') }}">
                <i class="now-ui-icons arrows-1_cloud-upload-94"></i>
                <p> {{ __("All Courses") }} </p>
              </a>
            </li>         
          </ul>
        </div>
      </li>
      <li>
        <a data-toggle="collapse" href="#docketSupsAndDefExamples">
          <i class="now-ui-icons education_paper"></i>
          <p>
            {{ __("Sups and Def Docket") }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="docketSupsAndDefExamples">
          <ul class="nav">            
            <li class="@if ($activePage == 'docket-indexSupsAndDef') active @endif">
              <a href="{{ route('docket.indexSupsAndDef') }}">
                <i class="now-ui-icons text_align-left"></i>
                <p> {{ __("View Students") }} </p>
              </a>
            </li>        
          </ul>
        </div>
      </li>
      <li>
        <a data-toggle="collapse" href="#docketNmczExamples">
          <i class="now-ui-icons education_paper"></i>
          <p>
            {{ __("NMCZ Docket") }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="docketNmczExamples">
          <ul class="nav">
            <li class="@if ($activePage == 'docket-indexNmcz') active @endif">
              <a href="{{ route('docket.indexNmcz') }}">
                <i class="now-ui-icons text_align-left"></i>
                <p> {{ __("View Students") }} </p>
              </a>
            </li>                    
          </ul>
        </div>
      </li>  
      @endif      
    </ul>
  </div>
</div>
