@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Manage Admissions',
    'activePage' => 'academics.ManageAdmissions',
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
                    <h4 class="card-title"> Manage Admissions : {{$results->count()}}</h4>
                    <button id="exportButton" class="btn btn-success">Export to Excel</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="admissionsTable">
                            <thead class="text-primary">
                                <tr>
                                    <th>All Applicants</th>
                                    <th>Study</th>
                                    <th>School</th>
                                    <th>Management</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $groupedResults = $results->groupBy('studyID');
                                @endphp
                                @foreach($groupedResults as $studyID => $group)
                                    @php
                                        $firstResult = $group->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $group->count() }}</td>
                                        <td>{{ $firstResult->Programme }}</td>
                                        <td>{{ $firstResult->School }}</td>
                                        <td>
                                            <a href="//edurole.lmmu.ac.zm/autoacceptance/show/{{$firstResult->studyID}}"> Selected Candidates</a>
                                            <a href="//edurole.lmmu.ac.zm/subjects/required/{{$firstResult->studyID}}"> Required Subjects</a>
                                        </td>
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

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('exportButton').addEventListener('click', function() {
            var wb = XLSX.utils.table_to_book(document.getElementById('admissionsTable'), {sheet: "Sheet JS"});
            XLSX.writeFile(wb, 'ManageAdmissions.xlsx');
        });
    });
</script>
@endsection