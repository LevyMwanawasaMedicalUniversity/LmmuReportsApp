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
      {{ __('SIS REPORTS') }}
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
      @if (auth()->user()->hasRole('Student') && !auth()->user()->hasAnyRole(['Administrator', 'Developer', 'Dosa', 'Examination', 'Academic', 'Finance']))
      
        <li class = "@if ($activePage == 'studentCourseRegistration') active @endif">
          <a href="{{ route('student.coursesRegistration', auth()->user()->name) }}">
            <i class="now-ui-icons gestures_tap-01"></i>
            <p>{{ __('Course Registration') }}</p>
          </a>
        </li>
        {{-- <li class = "@if ($activePage == 'studentExaminationDocket') active @endif">
          <a href="{{route('student.viewDocket')}}">
            <i class="now-ui-icons education_paper"></i>
            <p>{{ __('Docket') }}</p>
          </a>
        </li> --}}
        <li class = "@if ($activePage == 'studentExaminationDocket') active @endif">
          <a href="{{url('/student/viewSupplementaryDocket')}}">
            <i class="now-ui-icons education_paper"></i>
            <p>{{ __('Docket') }}</p>
          </a>
        </li>
        <li class = "@if ($activePage == 'studentExaminationResults') active @endif">
          <a href="{{ url('/students/exam/results/' . auth()->user()->name) }}">
            <i class="now-ui-icons education_hat"></i>
            <p>{{ __('Exam Results') }}</p>
          </a>
        </li>
        {{-- <li class = "@if ($activePage == 'studentExaminationResults') active @endif">
          <a href="{{ route('docket.studentsCAResults') }}">
            <i class="now-ui-icons education_hat"></i>
            <p>{{ __('CA Results') }}</p>
          </a>
        </li> --}}
      @endif
      @if ((auth()->user()->hasRole('Academics')) || (auth()->user()->hasRole('Administrator')) || (auth()->user()->hasRole('Dosa')) || (auth()->user()->hasRole('Developer')))
      <li class = "@if ($activePage == 'registrationCheck') active @endif">
        <a href="{{ route('academics.registrationCheck') }}">
          <i class="now-ui-icons education_glasses"></i>
          <p>{{ __('Registration Check') }}</p>
        </a>
      </li> 
      @endif
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
              <li class="@if ($activePage == 'roles') active @endif">
                <a href="{{ route('importOrUpdatexSisReportsEduroleData.admin') }}">
                  <i class="now-ui-icons loader_refresh"></i>
                  <p> {{ __("Sync With Edurole") }} </p>
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
              <li class="@if ($activePage == 'importGradesArchive') active @endif">
                <a href="{{ route('academics.GradesArchiveImport') }}">
                  <i class="now-ui-icons arrows-1_share-66"></i>
                  <p>{{ __('Import to Grades Archive') }}</p>
                </a>
              </li>
              <li class="@if ($activePage == 'academics.ManageAdmissions') active @endif">
                <a href="{{ route('academics.ManageAdmissions') }}">
                  <i class="now-ui-icons arrows-1_share-66"></i>
                  <p>{{ __('Manage Admissions') }}</p>
                </a>
              </li>               
              <li class="@if ($activePage == 'importGradesArchive') active @endif">
                <a href="{{ route('academics.EmailAnnouncement') }}">
                  <i class="now-ui-icons ui-1_email-85"></i>
                  <p>{{ __('Send Email') }}</p>
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
        <a data-toggle="collapse" href="#courseRegistration">
          <i class="now-ui-icons education_agenda-bookmark"></i>
          <p>
            {{ __("All Students") }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="courseRegistration">
          <ul class="nav">            
            <li class="@if ($activePage == 'courseRegistration') active @endif">
              <a href="{{ route('students.index') }}">
                <i class="now-ui-icons text_align-left"></i>
                <p> {{ __("View Students") }} </p>
              </a>
            </li> 
            <!-- <li class="@if ($activePage == 'students.import') active @endif">
              <a href="{{ route('students.import') }}">
                <i class="now-ui-icons arrows-1_cloud-upload-94"></i>
                <p> {{ __("Bulk Import Students") }} </p>
              </a>
            </li>  -->
            <li class="@if ($activePage == 'students.importSingleStudent') active @endif">
              <a href="{{ route('students.importSingleStudent') }}">
                <i class="now-ui-icons arrows-1_share-66"></i>
                <p> {{ __("Import Students") }} </p>
              </a>
            </li>       
          </ul>
        </div>
      </li>
      <li>
        <a data-toggle="collapse" href="#midAndNurse">
          <i class="now-ui-icons ui-2_favourite-28"></i>
          <p>
            {{ __("NURSING AND MIDWIFERY") }}
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="midAndNurse">
          <ul class="nav">            
            <li class="@if ($activePage == 'nurAndMid.viewStudents') active @endif">
              <a href="{{ route('nurAndMid.viewStudents') }}">
                <i class="now-ui-icons text_align-left"></i>
                <p> {{ __("View Students") }} </p>
              </a>
            </li> 
            <!-- <li class="@if ($activePage == 'students.import') active @endif">
              <a href="{{ route('students.import') }}">
                <i class="now-ui-icons arrows-1_cloud-upload-94"></i>
                <p> {{ __("Bulk Import Students") }} </p>
              </a>
            </li>  -->
            <li class="@if ($activePage == 'nurAndMid.import') active @endif">
              <a href="{{ route('nurAndMid.import') }}">
                <i class="now-ui-icons arrows-1_share-66"></i>
                <p> {{ __("Import Students") }} </p>
              </a>
            </li>       
          </ul>
        </div>
      </li>
      <li>
        <a data-toggle="collapse" href="#parentDocket">
            <i class="now-ui-icons education_paper"></i>
            <p>
                {{ __("EXAMINATION DOCKETS") }}
                <b class="caret"></b>
            </p>
        </a>
        <div class="collapse" id="parentDocket">
            <ul class="nav">
                <li>
                    <a data-toggle="collapse" href="#docketExamples">
                        <i class="now-ui-icons education_paper"></i>
                        <p>
                            {{ __("LMMU FINALS") }}
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
                            {{ __("SUPS OR DEFFERED") }}
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
                            {{ __("NMCZ EXAMS") }}
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse" id="docketNmczExamples">
                        <ul class="nav">
                            <li class="@if ($activePage == 'nmczRepeatImport') active @endif">
                                <a href="{{ route('docket.nmczRepeatImport') }}">
                                    <i class="now-ui-icons arrows-1_share-66"></i>
                                    <p> {{ __("Import Repeating Students") }} </p>
                                </a>
                            </li>
                            <li class="@if ($activePage == 'docket-indexNmcz') active @endif">
                                <a href="{{ route('docket.indexNmcz') }}">
                                    <i class="now-ui-icons text_align-left"></i>
                                    <p> {{ __("View Nmcz Students") }} </p>
                                </a>
                            </li>
                            <li class="@if ($activePage == 'docket-indexNmczRepeating') active @endif">
                                <a href="{{ route('docket.indexNmczRepeating') }}">
                                    <i class="now-ui-icons text_align-left"></i>
                                    <p> {{ __("View Repeating Students") }} </p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </li> 
      @endif      
    </ul>
  </div>
</div>
