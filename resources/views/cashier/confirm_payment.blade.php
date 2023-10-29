<style>
    .error-message {
        color: red;
        font-size: 12px;
    }

    .error-message-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 5px;
        height: 20px; /* Adjust the height as needed */
    }
</style>


<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead class="thead-dark">
            MNL230712KAO
                <tr>
                    <th class="text-right align-top" style="width:30%;">Reference No:</th>
                    <th class="text-break align-top" style="width:70%;">{{ $searchString != '' ? '' : $searchString }}</th>
                </tr>
            </thead>
            <tbody>
                @if (!is_null($application))
                {{Form::hidden('reference_no', $application->reference_no, ['id' => 'reference_no'])}}
                <tr class="border bg-white">
                    <td class="text-right">Name of Applicant:</td>
                    <td>{{ $application->lastname }}, {{ $application->firstname }} {{ $application->middlename }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Visa Type:</td>
                    <td>{{ $application->visa_type }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Visa Price:</td>
                    <td>{{ $application->visa_price }} ({{ $application->promo_code }})</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Handling Price:</td>
                    <td>{{ $application->handling_price }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Pick Up Fee:</td>
                    <td>{{ $application->pickup_fee }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Customer Type:</td>
                    <td>{{ $application->customer_type }}</td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Customer Company:</td>
                    <td>{{ $application->customer_company }}</td>
                </tr>
                @if ($application->customer_type != 'Corporate')
                <tr class="border bg-white ">
                    <td class="text-right">OR/ SI/ PR No.:</td>
                    <td>
                        {{Form::text('or_number', $application->or_number, ['class' => 'form-control text-center', 'id' => 'or_number', 'placeholder' => '(optional)', 'autocomplete' => 'off', 'disabled' => ($application->payment_status == 'UNPAID' ? false : true)])}}
                    </td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Mode of Payment:</td>
                    <td>
                        {{Form::select('payment_mode', $modeOfPayment, $application->payment_mode, ['class' => 'form-control text-center', 'id' => 'payment_mode', 'disabled' => ($application->payment_status == 'UNPAID' ? false : true)])}}
                    </td>
                </tr>
                <tr class="border bg-white">
                    <td class="text-right">Payment Request:</td>
                    <td>
                        {{Form::select('payment_request', $paymentRequest, $application->payment_request, ['class' => 'form-control text-center', 'id' => 'payment_request', 'disabled' => ($application->payment_status == 'UNPAID' ? false : true)])}}
                    </td>
                </tr>
                @endif

                @if ($application->payment_status == 'UNPAID')
                <tr class="border bg-white">
                    <td colspan="2" class="text-center">
                        <button class="btn btn-success" id="confirm_btn">Confirm Payment</button>
                        <button class="btn btn-danger" id="close_btn">Close</button>
                    </td>
                </tr>
                @else
                <tr class="border bg-white">
                    <td class="text-right">Payment Status:</td>
                    <td>
                        {{Form::select('payment_status', ['PAID' => 'PAID', 'UNPAID' => 'UNPAID'], $application->payment_status, ['class' => 'form-control', 'id' => 'payment_status', 'disabled' => ($application->payment_status == 'PAID')])}}
                    </td>
                </tr>
                <tr class="border bg-white">
                    <td colspan="3" class="text-center">
                        <button data-toggle="modal" data-target="#modify_payment" name="edit_btn" class="btn btn-warning" id="edit_btn">Modify Payment</button>
                        <button class="btn btn-dark" id="confirm_btn_modify">Confirm Payment</button>
                        <button class="btn btn-danger" id="close_btn">Close</button>
                    </td>
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
    @if ($application && $application->customer_type != 'Corporate')
    <a href="{{ route('cashier.download_acknowledgement_receipt_pdf', ['ref_no' => $application->reference_no]) }}" id="btn_receipt" class="btn btn-primary w-100" target="_blank">ACKNOWLEDGEMENT RECEIPT<span style="font-size:20px;" class="material-icons align-bottom"></span></a>
    @endif
</div>

<div id="modify_payment" class="modal fade">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h4 class="modal-title">Modify Payment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row p-1">
                        <div class="col-md-4 text-center">Approval Code: </div>
                        <div class="col-md-8">
                            <input class="form-control @error('approval_code') is-invalid @enderror" type="password" name="approval_code" required autofocus type="text" id="approval_code">
                        </div>
                    </div>
                    <div class="error-message-container">
                <div id="invalid_password_message" class="text-danger"></div>
            </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="send_otp">Send OTP</button>
                <button type="button" class="btn btn-success" id="modify_payment_btns">Modify Payment</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    

$(document).ready(function() {
    $('#confirm_btn').prop('disabled', true);
    $('#btn_receipt').addClass('disabled');

    if ($('#payment_mode').val() == '' || $('#payment_request').val() == '') {
        $('#btn_receipt').addClass('disabled');
    } else {
        $('#btn_receipt').removeClass('disabled');
    }
    // Add event listener to the "Modify Payment" button
    $('#edit_btn').click(function() {
        $('#payment_status').prop('disabled', true);
        $('#invalid_password_message').empty(); // Clear any previous error message
    });
});

$(document).on('change', '#payment_mode, #payment_request', function(){
    const pmode = $('#payment_mode').val();
    const preq = $('#payment_request').val();

    if(pmode == '' || preq == '')
    {
        $('#confirm_btn').prop('disabled', true);
        $('#btn_receipt').addClass('disabled');
    }
    else
    {
        $('#confirm_btn').prop('disabled', false);
        if(!$('#btn_receipt').hasClass('disabled')){
            $('#btn_receipt').removeClass('disabled');
        }
    }
});

// While modifying
$(document).ready(function() {
    // Get DOM elements
    var paymentStatusDropdown = $('#payment_status');
    var confirmButton = $('#confirm_btn_modify');

    // Listen for changes in the form fields
    $('input, select').change(function() {
        isChanged = true;
        if (paymentStatusDropdown.val() === 'PAID' || !isChanged) {
            confirmButton.prop('disabled', true);
        } else {
            confirmButton.prop('disabled', false);
        }
    });

    // Initially disable confirm button
    confirmButton.prop('disabled', true);
});




// Add a variable to track if OTP is currently being sent
var isSendingOTP = false;

$(document).one('click', '#send_otp', function() {
    if (!isSendingOTP) {
        isSendingOTP = true; // Set the flag to indicate OTP request is in progress
        $.ajax({
            url: "../application_batches/generate_approval_code",
            success: function(response) {
                $('#invalid_password_message').text("One Time Password is sent");
                console.log("OTP Sent!");
            },
            complete: function() {
                // Enable the button after the request is complete
                isSendingOTP = false;
            }
        });
    }
});

function modify_payment_btn() {
    var approvalCode = $('#approval_code').val();

    // Check if the approval code is empty
    if (approvalCode.trim() === '') {
        $('#invalid_password_message').text("Field cannot be empty");
        return;
    }

    $.ajax({
        url: "../cashier/check_otp_code",
        data: { approval_code: approvalCode },
        success: function(response) {
            if (response.status === "success") {
                // If the approval code is valid, proceed with the modification of payment
                $.ajax({
                    url: "../cashier/confirm_payment",
                    data: { approval_code: approvalCode },
                    success: function(message) {
                        $('#btn_receipt').addClass('disabled');
                        $('#payment_status').prop('disabled', false); // Enable payment status

                         // Close the modal
                        $('#modify_payment').modal('hide');

                        // Remove the disabled state for the button
                        $('#confirm_btn_modify').prop('disabled', false);
                    },
                    error: function() {
                        // Handle error if confirm_payment AJAX request fails
                        // For example, show an error message to the user
                        $('#invalid_password_message').text("Payment confirmation failed.");
                    }
                });
            } else if (response.message === "OTP has expired") {
                // The provided OTP has expired
                $('#invalid_password_message').text("OTP has expired. Please request a new OTP.");
                $('#approval_code').val('');
            } else {
                // The provided OTP is incorrect
                $('#invalid_password_message').text("Incorrect OTP");
                $('#approval_code').val('');
            }
        },
        error: function() {
            // Handle error if checkOtpCode AJAX request fails
            // For example, show an error message to the user
            $('#invalid_password_message').text("OTP verification failed.");
        }
    });
}


    $(document).on('click','#modify_payment_btns', function(){
		modify_payment_btn();
	});


	// $('#modify_payment').on('hidden.bs.modal', function(){
	// 	$('#modify_payment div div.modal-body').html('<div class="spinner-border text-info"></div>');
	// });



$(document).on('click', '#confirm_btn_modify', function(){
			$('#vpr_number').tooltip('hide');
			if($('#vpr_number').val() == '')
			{
				$('#vpr_number').tooltip('show');
				$('#vpr_number').focus();
				return;
			}
			$.ajax({
				url: "../cashier/customer_payment_unpaid",
				data: {or_number:$('#or_number').val(),reference_no:$('#reference_no').val(),payment_mode:$('#payment_mode').val(), payment_request:$('#payment_request').val()},
				success: function(message){
					$('#confirm_payment_message').html(message);
					$('#confirm_payment_modal').modal();
					$('#btn_receipt').removeClass('disabled');
				}
			});
		});


</script>
