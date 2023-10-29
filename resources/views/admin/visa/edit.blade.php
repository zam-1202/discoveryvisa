@extends('layouts.app')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
    			.scrollable-div {
			max-height: 200px; /* Adjust the height as needed */
			overflow-y: auto;
			}
</style>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header bg-success text-white text-center"><h1>{{ __('Update Visa Type') }}<h1></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.visa_types.update', $result->id) }}">
                        @csrf

						<div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control text-center @error('name') is-invalid @enderror" name="name" value="{{ old('name', $result->name) }}" required autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="branch_visa" class="col-md-4 col-form-label text-md-right">{{ __('Branch Visa') }}</label>

                            <div class="col-md-6">
                                <select class="form-control text-center @error('branch_visa') is-invalid @enderror" name="branch_visa" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->code }}" {{ old('branch_visa', $result->branch_visa) == $branch->code ? 'selected' : '' }}>
                                            {{ $branch->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('branch_visa')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>





                        <div class="form-group row">
                            <label for="handling_fee" class="col-md-4 col-form-label text-md-right">{{ __('Handling Fee') }}</label>

                            <div class="col-md-6">
                                <input id="handling_fee" type="text" class="form-control text-center @error('handling_fee') is-invalid @enderror" name="handling_fee" value="{{ old('handling_fee', $result->handling_fee) }}" required>

                                @error('handling_fee')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="visa_fee" class="col-md-4 col-form-label text-md-right">{{ __('Visa Fee') }}</label>

                            <div class="col-md-6">
                                <input id="visa_fee" type="text" class="form-control text-center @error('visa_fee') is-invalid @enderror" name="visa_fee" value="{{ old('visa_fee', $result->visa_fee) }}">

                                @error('visa_fee')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 mt-3 text-center">
                              <label for="documents_submitted">Documents Required:</label>
                              {{Form::hidden('documents_submitted')}}
                              <div class="table-responsive">
                                  <table class="table table-sm table-bordered">
                                      <thead class="thead-light">
                                          <th style="width:33.33%;" class="bg-success text-white">FILIPINO DOCUMENTS</th>
                                          <th style="width:33.33%;" class="bg-info text-white">JAPANESE DOCUMENTS</th>
                                          <th style="width:33.33%;" class="bg-dark text-white">FOREIGNER DOCUMENTS</th>
                                      </thead>
                                      <tbody>
                                          <tr>
                                            <td class="bg-success text-left">
                                            <div class="scrollable-div">
                                                <ul class="list-group" id="filipino_documents">
                                                    @foreach ($docs_filipino as $value)
                                                        <li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' {{ in_array($value->id, $selected_docs) ? 'checked' : '' }}/> {{ $value->name }} </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            </td>
                                            <td class="bg-info text-left">
                                            <div class="scrollable-div">
                                                <ul class="list-group" id="japanese_documents">
                                                    @foreach ($docs_japanese as $value)
                                                        <li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' {{ in_array($value->id, $selected_docs) ? 'checked' : '' }}/> {{ $value->name }} </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            </td>
                                            <td class="bg-dark text-left">
                                            <div class="scrollable-div">
                                                <ul class="list-group" id="foreign_documents">
                                                    @foreach ($docs_foreign as $value)
                                                        <li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' {{ in_array($value->id, $selected_docs) ? 'checked' : '' }}/> {{ $value->name }} </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            </td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                            </div>
                          </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-3 offset-md-5">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Save') }}
                                </button>

								<a href="{{route('admin.visa_types')}}" class="btn btn-danger">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){

        var checkboxes = $('input[name="submitted_documents"]:checked');
        var output = [];

        checkboxes.each(function(){
            output.push($(this).val());
        });

        $('input[name="documents_submitted"]').val(output);


        $(document).on('change', 'input[name="submitted_documents"]', function(){
			var checkboxes = $('input[name="submitted_documents"]:checked');

			var output = [];

			checkboxes.each(function(){
				output.push($(this).val());
			});

			$('input[name="documents_submitted"]').val(output);
		});
    });
</script>
@endsection
