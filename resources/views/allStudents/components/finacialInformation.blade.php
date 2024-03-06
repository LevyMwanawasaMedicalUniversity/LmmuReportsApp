<div class="card">
    <div class="card-header">
        <h4 class="card-title">PAYMENT INFORMATION</h4>
        <h5>{{$studentDetails->FirstName}}  {{$studentDetails->Surname}}</h5>
        <p>Note that your Registration is based on your payments made in 2024</p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead class="text-primary">
                    <tr>
                        <th>Student Number</th>                        
                        <th>Total Payments made in 2024</th>
                        <th>
                            {{ $studentDetails->Amount < 0 ? 'Current Balance' : 'Outstanding Balance' }}
                        </th>
                        <th>Latest Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $studentId }}</td>                        
                        <td id="payments2024">K{{ $studentsPayments->TotalPayment2024 ?? 0 }}</td>
                        <td style="font-weight:bold; color :{{ $studentDetails->Amount >= 0 ? 'red' : 'green' }};">
                            K {{$studentDetails->Amount}}
                        </td>
                        <td>@isset($studentsPayments->LatestInvoiceDate) {{ $studentsPayments->LatestInvoiceDate }} @endisset</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>