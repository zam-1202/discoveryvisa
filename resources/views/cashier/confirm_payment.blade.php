<div class="row">
    <div class="col-md-8">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="text-right align-top" style="width:30%;">Reference No:</th>
                    <th class="text-break align-top" style="width:70%;">{{ $searchString }}</th>
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
                @if ($application->customer_type != 'Corporate')
                <tr class="border bg-white">
                    <td class="text-right">OR/ SI/ PR No.:</td>
                    <td>
                        {{Form::text('or_number', $application->or_number, ['class' => 'form-control', 'id' => 'or_number', 'placeholder' => '(optional)', 'autocomplete' => 'off', 'disabled' => ($application->payment_status == 'UNPAID' ? false : true)])}}
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
    <a href="{{ route('cashier.download_acknowledgement_receipt_pdf', ['ref_no' => $application->reference_no]) }}" id="btn_receipt" class="btn btn-primary w-100">ACKNOWLEDGEMENT RECEIPT<span style="font-size:20px;" class="material-icons align-bottom"></span></a>

    @endif
</div>

<div class="modal fade" id="modify_payment">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-secondary text-white">
				<h4 class="modal-title">Modify Payment</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="spinner-border text-info"></div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-success" id="modify_payment_btns">Modify Payment</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
        console.log("clicked")
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

function modify_payment_btn() {
    var approvalCode = $('#approval_code').val();
    $.ajax({
        url: "../cashier/check_approval_code",
        data: { approval_code: approvalCode },
        success: function(response) {
            if (response.status === "success") {
                // If the approval code is valid, continue with the modification of payment
                $.ajax({
                    url: "../cashier/confirm_payment",
                    data: { approval_code: approvalCode },
                    success: function(message) {
                        $('#btn_receipt').addClass('disabled');
                        $('#payment_status').prop('disabled', false); // Enable payment status

                        // Close the modal
                        $('#modify_payment').modal('hide');

                        // Remove the disable for the button
                        $('#confirm_btn_modify').prop('disabled', true);

                    }
                });
            } else {
                // If the approval code is invalid, show an error message
                $('#approval_code').addClass('invalid-field'); // Add the CSS class to make the field red
                if (response.status === 'Request has been rejected') {
                    $('#modify_payment div.modal-dialog').addClass('shake');
                    setTimeout(function() {
                        $('#modify_payment div.modal-dialog').removeClass('shake');
                        $('#modify_payment div div.modal-body').html("<div class='text-center text-danger'>Request has been rejected</div>");
                    }, 500);
                } else {
                    $('#modify_payment div.modal-dialog').addClass('shake');
                    setTimeout(function() {
                        $('#modify_payment div.modal-dialog').removeClass('shake');
                        $('#modify_payment div div.modal-body').html("<div class='text-center text-danger'>Incorrect Password</div>");
                    }, 500);
                }
            }
        },
        error: function() {
            // Handle the error condition
            console.log('Error occurred during AJAX request');
            // Show a generic error message to the user
            $('#modify_payment div.modal-dialog').addClass('shake');
            setTimeout(function() {
                $('#modify_payment div.modal-dialog').removeClass('shake');
                $('#modify_payment div div.modal-body').html("<div class='text-center text-danger'>An error occurred. Please try again later.</div>");
            }, 500);
        }
    });
}





$('#modify_payment').on('hidden.bs.modal', function(){
    $('#modify_payment div div.modal-body').html('<div class="spinner-border text-info"></div>');
    // $('#payment_status').prop('disabled', false); // Disable payment status when modal is closed
});


$(document).on('click','button[name="edit_btn"]',function(){
		var current_row = $(this).closest('tr');
		var modify_payment_html = "<div class='container'>" +
                                   "<div class='row p-1'>" +
								   "<div class='col-md-4 text-right'>Approval Code: </div>" +
								   "<div class='col-md-8'><input class='form-control text-center' type='text' id='approval_code'></div>" +
								   "</div>" +
								   "</div>";
		$('#modify_payment div div.modal-body').html(modify_payment_html);
	});

    $(document).on('click','#modify_payment_btns', function(){
		modify_payment_btn();
	});

	$('#modify_payment').on('hidden.bs.modal', function(){
		$('#modify_payment div div.modal-body').html('<div class="spinner-border text-info"></div>');
	});



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