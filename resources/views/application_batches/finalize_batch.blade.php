@extends('layouts.app')

@section('content')

<div class="container">
        <div class="row justify-content-center">
                <div class="col-md-8">
                        @if (session('status'))
                                <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                                        <strong>{{ session('status') }}</strong>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                        </button>
                                </div>
                        @endif
                        <div class="card">
                                <div class="card-header bg-primary text-white text-center">
                                        <h4 class="font-weight-bold">FINALIZE Daily Batch of Applications</h4>
                                </div>
                                <div class="card-body text-center">
                                        <span class="text-danger font-weight-bold">WARNING!!</span>
                                        <br>
                                        <span class="text-danger"> This process can only be done once per day.</span>
                                        <br>
                                        <span class="text-danger"> Use the <a href="{{route('application_batches.checklist')}}" class="font-weight-bold">GENERATE Checklist</a> function to check the applications that will be included in this final batch.</span>
                                        <br>
                                        <br>
                                        <button id="finalize_batch_btn" class="btn btn-danger" data-target="#finalize_batch" data-toggle="modal">FINALIZE BATCH</button> <a href="{{url('/')}}" class="btn btn-primary">Back</a>
                                </div>
                        </div>
                </div>
        </div>
</div>

<div id="finalize_batch" class="modal fade">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h4 class="modal-title">Admin Approval</h4>
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
                <button type="button" class="btn btn-success" id="modify_payment_btns">Confirm</button>
            </div>
        </div>
    </div>
</div>


@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {
    var isSendingOTP = false;

    $('#send_otp').one('click', function() {
        if (!isSendingOTP) {
            isSendingOTP = true;
            $.ajax({
                url: "../application_batches/generate_approval_code",
                success: function(response) {
                    $('#invalid_password_message').text("One Time Password is sent");
                },
                complete: function() {
                    isSendingOTP = false;
                }
            });
        }
    });

    function finalizeBatch() {
        window.location.href = "{{ route('finalize_batch') }}";
    }

    function confirmation_btn() {
        var approvalCode = $('#approval_code').val().trim();

        if (approvalCode === '') {
            $('#invalid_password_message').text("Password cannot be empty");
            return;
        }

        $.ajax({
            url: "../application_batches/check_otp_code",
            data: { approval_code: approvalCode },
            success: function(response) {
                var message = "Validation failed.";

                if (response.status === "success") {
                    message = "Success. OTP verified.";
                    $('#finalize_batch').modal('hide');
                    $('#modify_payment').modal('hide');
                    $('#confirm_btn_modify').prop('disabled', false);
                    finalizeBatch();
                } else if (response.message === "OTP has expired") {
                    message = "OTP has expired. Please request a new OTP.";
                    $('#approval_code').val('');
                } else {
                    message = "Incorrect OTP";
                    $('#approval_code').val('');
                }

                $('#invalid_password_message').text(message);
            },
            error: function() {
                $('#invalid_password_message').text("OTP verification failed.");
            }
        });
    }

    $(document).on('click', '#modify_payment_btns', function() {
        confirmation_btn();
    });

    $('#finalize_batch_btn').on('click', function() {
        if ($('#confirmation_btn').hasClass('btn-success')) {
            confirmation_btn();
        }
    });
});




    
</script>

