@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white text-center"><h1>{{ __('Update Promo Code') }}<h1></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.promo_codes.update', $promo->id) }}">
                        @csrf

						<div class="form-group row">
                            <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}</label>

                            <div class="col-md-6">
                                <input id="code" type="text" class="form-control text-center @error('code') is-invalid @enderror" name="code" value="{{ $promo->code }}" required readonly>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="discount" class="col-md-4 col-form-label text-md-right">{{ __('Discount') }}</label>

                            <div class="col-md-6">
                                <input id="discount" type="text" class="form-control text-center @error('discount') is-invalid @enderror" name="discount" value="{{ old('discount') ?? $promo->discount }}" pattern="[1-9]\d*%?" title="Please enter valid discount value" required autofocus>

                                @error('discount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="expiration_date" class="col-md-4 col-form-label text-md-right">{{ __('Expiration Date') }}</label>

                            <div class="col-md-6">
                                <input id="expiration_date" type="date" class="form-control text-center @error('expiration_date') is-invalid @enderror" name="expiration_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('expiration_date') ?? $promo->expiration_date }}" required>

                                @error('expiration_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="quantity" class="col-md-4 col-form-label text-md-right">{{ __('Quantity') }}</label>

                            <div class="col-md-6">
                                <input id="quantity" type="text" class="form-control text-center @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity') ?? $promo->max_quantity }}" pattern="[1-9]\d*%?" title="Please enter valid discount value" required>
                                @error('quantity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-3 offset-md-5">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Save') }}
                                </button>

								<a href="{{route('admin.promo_codes')}}" class="btn btn-danger">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
