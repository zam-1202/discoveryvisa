@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white text-center"><h1>{{ __('Update User') }}<h1></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.update', $user->id) }}">
                        @csrf

						<div class="form-group row">
                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control text-center @error('username') is-invalid @enderror" name="username" value="{{ old('username') ?? $user->username }}" required autofocus>

                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control text-center @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $user->name }}" required autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

						<div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>

                            <div class="col-md-6">
								{{Form::select('role', $role_array, $user->role, ['class' => 'form-control', 'id' => 'role'])}}

								{{--
							    <input id="role" type="text" class="form-control @error('role') is-invalid @enderror" name="role" value="{{ old('role') }}" required autofocus>
								--}}

                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

						<div class="form-group row">
                            <label for="branch" class="col-md-4 col-form-label text-md-right">{{ __('Branch') }}</label>

                            <div class="col-md-6">
								{{Form::select('branch', $branch_list, $user->branch, ['class' => 'form-control', 'id' => 'branch'])}}

								{{--
                                <input id="branch" type="text" class="form-control @error('branch') is-invalid @enderror" name="branch" value="{{ old('branch') }}" required autofocus>
								--}}

                                @error('branch')
                                    <span class="invalid-feedback" branch="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control text-center @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? $user->email }}" required>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-3 offset-md-5">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Update') }}
                                </button>

								<a href="{{route('admin.users')}}" class="btn btn-danger">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
