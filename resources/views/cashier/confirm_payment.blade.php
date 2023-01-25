<div class="row">
    <div class="col-md-8">
        <table class="table">
            <thead class="thead-dark">
                <th class="text-right align-top" style="width:30%;">Reference No: </th>
                <th class="text-break align-top" style="width:70%;">{{ $searchString }}</th>
            </thead>
            <tbody>
            @if(!is_null($application))
                {{Form::hidden('reference_no', $application->reference_no, ['id' => 'reference_no'])}}
                <tr class="border bg-white">
                    <td class="text-right">Name of Applicant: </td>
                    <td>{{ $application->lastname}}, {{ $application->firstname }} {{ $application->middlename }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Visa Type: </td>
                    <td>{{ $application->visa_type }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Visa Price: </td>
                    <td>{{ $application->visa_price }} ({{ $application->promo_code }})</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">OR/ SI/ PR No.: </td>
                    <td>{{Form::text('or_number', $application->or_number, ['class' => 'form-control', 'id' => 'or_number', 'placeholder' => '(optional)', 'autocomplete' => 'off', 'disabled' => ($application->payment_status == 'UNPAID' ? false : true)])}}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Mode of Payment: </td>
                    <td>{{Form::select('payment_mode',$modeOfPayment, $application->payment_mode, ['class' => 'form-control text-center', 'id' => 'payment_mode', 'disabled' => ($application->payment_status == 'UNPAID' ? false : true)])}}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Payment Request: </td>
                    <td>{{Form::select('payment_request',$paymentRequest, $application->payment_request, ['class' => 'form-control text-center', 'id' => 'payment_request', 'disabled' => ($application->payment_status == 'UNPAID' ? false : true)])}}</td>
                </tr>
                @if($application->payment_status == 'UNPAID')
                <tr class="border bg-white">
                    <td colspan="2" class="text-center">
                        <button class="btn btn-success" id="confirm_btn">Confirm Payment</button>
                        <button class="btn btn-danger" id="close_btn">Close</button>
                    </td>
                </tr>
                @else
                <tr class="border bg-white">
                    <td class="text-right">Payment Status: </td>
                    <td>{{ $application->payment_status }}</td>
                </tr>
                <tr class="border bg-white">
                    <td colspan="2" class="text-center"><button class="btn btn-danger" id="close_btn">Close</button></td>
                </tr>
                @endif
            @else
                <tr>
                    <td colspan="2" class="text-center border bg-white text-danger"><h1>No Data Found</h1></td>
                </tr>
                <tr class="border bg-white">
                    <td colspan="2" class="text-center"><button class="btn btn-danger" id="close_btn">Close</button></td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <table class="table">
            <thead>
                <th class="text-center align-top" style="width:100%;" colspan="2">Charges</th>
            </thead>
            <tbody>
            @if(!is_null($application))
                <tr class="border bg-white">
                    <td class="text-right">Visa Fee: </td>
                    <td>{{ number_format($visaType->visa_fee, 2) }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Handling Fee: </td>
                    <td>{{ number_format($visaType->handling_fee, 2) }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">12& VAT: </td>
                    <td>{{ number_format((($visaType->handling_fee + $visaType->visa_fee) / 1.12) * 0.12,2) }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Net: </td>
                    <td>{{ number_format(($visaType->handling_fee + $visaType->visa_fee) / 1.12, 2) }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Sub Total: </td>
                    <td>{{ number_format($visaType->handling_fee + $visaType->visa_fee, 2) }}</td>
                </tr>
            </tbody>
            @endif
        </table>

        <button class="btn btn-primary w-100" id="acknowledgement_receipt">ACKNOWLEDGEMENT RECEIPT</button>
    </div>
</div>


