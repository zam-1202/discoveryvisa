@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">


<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-12">
			@if(session()->get('status'))
			<div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
				<strong>{{ session('status') }}</strong>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			@endif

			<div class="card">
				<div class='card-header text-center bg-primary text-white'>
					<h1>Visa Application Batches</h1>
				</div>

				<div class="col-md-8 offset-md-2">
						<input type="text" id="batchNum" name="batchNum" class="form-control text-center" 
						placeholder="Type batch number to search">
				</div>

				<div id='applicationBatch_list'>
					@include('application_batches.applicationBatch_list')
				</div>


					</table>
					

					
					<div class="row">
						<div class="col-md-12 text-center">
							<a href="{{url('/')}}" class="btn btn-danger">Back</a>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>



@endsection

@section('scripts')
<script type="text/javascript">

$(document).ready(function()
	{
		searchBatchNum(1);

		function searchBatchNum(page)
		{
			$.ajax({
				url: "application_batches/applicationBatch_list?page=" + page,
				data: { searchString: $('#batchNum').val() },
				headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
				success: function(data)
				{
					$('#applicationBatch_list').html(data);
				}
			});
		}

		// $(document).on('click','.pagination a', function(event)
		// {
		// 	event.preventDefault();
		// 	var page = $(this).attr('href').split('page=')[1];
		// 	searchBatchNum(page);
		// });


		$(document).on('keyup', '#batchNum', function(){
			searchBatchNum(1);
		});

	});

</script>


@endsection

