<!DOCTYPE html>

<style>
    .row {
  margin-top: 10px;
}

h6 {
  margin-bottom: 10px;
}


    </style>

<html>
  <head>
    <meta charset="utf-8">
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet" >
    <title></title>

  </head>
  <body style="background-color: white">
    <div class="row">
        <div class="col-7">
            <div class="bg-dark text-center">
                <h6 class="text-white" style="font-size: 20px;">ACKNOWLEDGEMENT</h6>
            </div>
        </div>
        <div class="offset-7 col-5">
            <div class="text-center">
                <h6 style="font-size: 10px;">*Please bring this receipt to claim your passport.</h6>
                <img src="{{ asset('img/logo.png') }}" alt="tag">
                <h6 class="m-0" style="font-size: 11px;">(02) 892-2849 / 818-7716 (Manila Office)</h6>
                <h6 class="m-0" style="font-size: 11px;">(032) 341-1923 / 341-1935 (Cebu Office)</h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="text-left">
                <h6 style="font-size: 11px;">REFERENCE #: {{ $application->reference_no }}</h6>
                <h6 style="font-size: 11px;">APPLICANT NAME: {{ $application->lastname }}, {{ $application->firstname }} {{ $application->middlename }}</h6>
                <h6 style="font-size: 11px;">VISA FEE: {{ number_format($application->visa_price, 2, '.', ',') }}PHP</h6>      
                <h6 style="font-size: 11px;">HANDLING FEE: {{ number_format($application->handling_price, 2, '.', ',') }}PHP</h6>
                <h6 style="font-size: 11px;">PICK UP FEE: {{ number_format($application->pickup_fee, 2, '.', ',') }}PHP</h6>      
                <h6 style="font-size: 11px;">TOTAL AMOUNT OF: {{ number_format($application->visa_price + $application->handling_price + $application->pickup_fee, 2, '.', ',') }}PHP</h6>                
                <h6 style="font-size: 11px;">VISA TYPE: {{ $application->visa_type }}</h6>
            </div>
        </div>
        <div class="col-6 offset-6">
            <div class="text-left">
                <h6 style="font-size: 11px;">IN CHARGE: {{ $application->payment_received_by }}</h6>
                <h6 style="font-size: 11px;">DATE OF PAYMENT: {{ date('m/d/Y', strtotime($application->payment_date)) }}</h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="text-right">
                <h6 style="font-size: 13px;">{{ date('m/d/Y') }}</h6>
                <h6 class="mb-4" style="font-size: 13px;">Thank you very much!!!</h6>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="text-left">
                <h6 class="text-white bg-dark" class="mb-2" style="font-size: 15px;">WE HAVE RECEIVED THE FOLLOWING DOCUMENTS FOR VISA PROCESS</h6>
            </div>
        </div>
    </div>
<div class="row">
    <div class="col-6">
        <div class="text-left">
            @foreach ($docs->slice(0, round(count($docs) / 2)) as $doc)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="docs[]" value="{{ $doc->id }}" id="{{ 'doc-' . $doc->id }}" checked>{{ $doc->name }}</input>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-6 offset-6">
        <div class="text-left">
            @foreach ($docs->slice(round(count($docs) / 2)) as $doc)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="docs[]" value="{{ $doc->id }}" id="{{ 'doc-' . $doc->id }}" checked>{{ $doc->name }}</input>
                </div>
            @endforeach
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-12">
            <h6 style="font-size: 15px; margin-top: 1%" class="mb-3">TERMS AND CONDITIONS</h6>
    
            <h6 style="font-size: 11px;">1. We will strictly implements the visa procedure of Discovery Tour, Inc starting from receipt up to claiming of the documents by applicant.</h6>
            <h6 style="font-size: 11px;">2. All necessary documents must be prepared by the applicant, the submission and claiming of passports must be done in person.</h6>
            <h6 style="font-size: 11px;">
                3. We will not accept or receive for processing of visa thru Third Parties or Representatives who are assisting applicants for monetary payments, profit or
                commissions. The granting or denial of the Visa is the exclusive prerogative of the Japan Embassy in Manila and Discovery Tour, Inc will not entertain any
                questions in this regard.
            </h6>
            <h6 style="font-size: 11px;">
                4. Please be informed that the applicant is entering into a Contact with Discovery Tour, Inc to engage our services for Japan Visa.
                These documents will remain strictly confidential. We assume the responsibility in protecting the personal information that will be handled by Discovery Tour, Inc
                while processing for the Visa until such time the documents can be claimed by the applicants.
            </h6>
            <h6 style="font-size: 11px;">
                5. The processing fee received by the company is non-refundable regardless of cancellation of application by the applicant or denial  of Visa by the Embassy.
            </h6>
            <h6 style="font-size: 11px;">
                6. Note: Purchasing of ticket prior to application of Japan visa is not advisable.<br>
                In case the visa is not granted or issued at the date of your departure, please be inform that we are not liable in any ways.
            </h6>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="text-center mt-3 ml-2">
                <hr class="mb-0">
                <h6 style="font-size: 15px;">Date</h6>
            </div>
        </div>
        <div class="col-4 offset-7">
            <div class="text-center mt-3">
                <hr class="mb-0">
                <h6 style="font-size: 15px;">Signature over printed name</h6>
            </div>
        </div>
    </div>
  </body>
</html>

