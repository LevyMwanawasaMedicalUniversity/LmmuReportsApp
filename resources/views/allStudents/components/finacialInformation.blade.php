<div class="card">
                        <div class="card-header">
                            <h4 class="card-title">PAYMENT INFORMATION</h4>
                            <P>Note that your Registration is based on your payments made in 2024</P>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Account</th>
                                        <th>Latest Invoice</th>
                                        {{-- <th>Total Payments Made</th> --}}
                                        <th>Total Payments made in 2024</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $studentId }}</td>
                                        <td>@isset($studentsPayments->LatestInvoiceDate) {{ $studentsPayments->LatestInvoiceDate }} @endisset</td>
                                        {{-- <td>@isset($studentsPayments->TotalPayments) K{{ $studentsPayments->TotalPayments }} @endisset</td> --}}
                                        <td id="payments2024">K{{ $studentsPayments->TotalPayment2024 ?? 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>