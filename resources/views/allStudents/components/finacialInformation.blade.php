<div class="card">
    <div class="card-header">
        <h4 class="card-title">PAYMENT INFORMATION</h4>
        <h5 style="font-weight:bold; color :{{ $studentDetails->Amount >= 0 ? 'red' : 'green' }};">{{$studentDetails->FirstName}}  {{$studentDetails->Surname}}</h5>
        <p><strong>Note that your Registration is based on your payments made in 2025</strong></p>
        @if ($studentDetails->Amount > 0)
            <p style="font-weight:bold; color: red;">Note that your Outstanding balance should be cleared and registration should sit on Edurole</p>
        @endif
    </div>
    <script>
        var payments2024 = {{ isset($studentsPayments->TotalPayment2025) ? $studentsPayments->TotalPayment2025 : '0' }};
        
        var balance = {{ isset($amountAfterInvoicing) ? $amountAfterInvoicing : $studentDetails->Amount }};

        var actualBalance = {{ $actualBalance }};
    </script>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead class="text-primary">
                    <tr>
                        <th>Student Number</th>                        
                        <th>Total Payments made in 2025</th>
                        @if($studentStatus == 5)
                        <th>
                            {{ $amountAfterInvoicing < 0 ? 'Current Balance' : 'Balance Due September' }}
                        </th>

                        @else
                        <th>
                            {{ $studentDetails->Amount < 0 ? 'Current Balance' : 'Balance Due ' }}
                        </th>
                        @endif
                        {{-- <th>Latest Invoice</th> --}}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $studentId }}</td>                        
                        <td id="payments2024">K{{ $studentsPayments->TotalPayment2025 ?? 0 }}</td>
                        @if($studentStatus == 5)
                        <td style="font-weight:bold; color :{{ $amountAfterInvoicing >= 0 ? 'red' : 'green' }};">
                            K {{$amountAfterInvoicing}}
                        </td>
                        @else
                        <td style="font-weight:bold; color :{{ $actualBalance >= 0 ? 'red' : 'green' }};">
                            K {{$actualBalance}}
                        </td>
                        @endif
                        {{-- <td>@isset($studentsPayments->LatestInvoiceDate) {{ $studentsPayments->LatestInvoiceDate }} @endisset</td> --}}
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>