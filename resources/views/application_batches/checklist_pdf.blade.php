<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>

	<style>
	table {
    border-collapse: collapse;
  	}
	
	td, th {
		padding: 8px;
		text-align: left;
		word-wrap: break-word;
		vertical-align: top;
  		border: 1px solid black;
	}

	.customer_type {
		font-weight: bold;
		font-size: 11px;
	}
	

	h1, h3 {
		text-align: center;
		margin: 0;
	}
		
	.clearfix:after {
		content: "";
		display: table;
		clear: both;
	}

	.table-container {
		column-count: 2;
		column-gap: 20px;
	/* justify-content: space-between;
	width: 50%; */
	}

	.page-break {
    page-break-before: always;
	}
	

	</style>
  </head>

  <body>
	<div class="container" style="display:flex; align-items:center; text-align: center;">
		<div>
				<h1 style="margin: 0;">Checklist for {{$branch}}</h1>
				<h3 style="margin: 0;">{{$date}}</h3>
		</div>
		<img src="data:image/png;base64, {{base64_encode(QrCode::format('png')->errorCorrection('H')->generate('http://192.168.1.10:8080/discovery-visa-system/public/application_batches/checklist')) }}" style="float: left; margin-right: 20px; width: 70px; height: 70px;">
	</div>


<div class="class-container">
<div style="margin-top: 20px;">
	<table class= "table-container">
		<tr>
			<th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2"  >Walk-in</th>
		</tr>
        <tr>
			<td class="customer_type" style="font-size: 11px; width: 15px;">No.</td>
			<td class="customer_type" style="font-size: 11px; width: 200px;">Applicant</td>
		</tr>
        @if($walkin_applications->count() > 0)
			@php $i = 1; @endphp
				@foreach($walkin_applications as $walkin)
					@if(in_array($walkin->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
						<tr>
							<td style="font-size: 10px; padding: 5px;">{{$i}}</td>
							<td style="font-size: 10px; padding: 5px;">{{$walkin->lastname}}, {{$walkin->firstname}} {{$walkin->middlename}}</td>
						</tr>
					@endif
					@php $i++; @endphp
				@endforeach
			@else
		@endif
    </table>
</div>


<!-- <div class="page-break"> -->

<div style="margin-top: 20px;">
	<table style= "table-container">
		<tr>
			<th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2"  >Corporate</th>
		</tr>	
		<tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px; width: 200px;">Applicant</td>
		</tr>
		@if($corporate_applications->count() > 0)
			@php $i = 1; @endphp
				@foreach($corporate_applications as $corporate)
					@if(in_array($corporate->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
						<tr>
							<td style="font-size: 10px; padding: 5px;">{{$i}}</td>
							<td style="font-size: 10px; padding: 5px;">{{$corporate->lastname}}, {{$corporate->firstname}} {{$corporate->middlename}}</td>
						</tr>
					@endif
					@php $i++; @endphp
				@endforeach
			@else
		@endif
	</table>
</div>

<!-- <div class="page-break"> -->

<div style="margin-top: 20px;">
	<table style= "table-container">
		<tr>
			<th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2"  >POEA</th>
		</tr>	
        <tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px; width: 200px;">Applicant</td>
		</tr>
        @if($poea_applications->count() > 0)
			@php $i = 1; @endphp
				@foreach($poea_applications as $poea)
					@if(in_array($poea->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
						<tr>
							<td style="font-size: 10px; padding: 5px;">{{$i}}</td>
							<td style="font-size: 10px; padding: 5px;">{{$poea->lastname}}, {{$poea->firstname}} {{$poea->middlename}}</td>
						</tr>
					@endif
					@php $i++; @endphp
				@endforeach
			@else
		@endif
    </table>
</div>

<!-- <div class="page-break"> -->

<div style="margin-top: 20px;">
	<table class= "table-container">
		<tr>
			<th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2"  >PIATA</th>
		</tr>
        <tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px; width: 200px;">Applicant</td>
		</tr>
        @if($piata_applications->count() > 0)
			@php $i = 1; @endphp
				@foreach($piata_applications as $piata)
					@if(in_array($piata->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
						<tr>
							<td style="font-size: 10px; padding: 5px;">{{$i}}</td>
							<td style="font-size: 10px; padding: 5px;">{{$piata->lastname}}, {{$piata->firstname}} {{$piata->middlename}}</td>
						</tr>
					@endif
					@php $i++; @endphp
				@endforeach
			@else
		@endif
    </table>
</div>

<!-- <div class="page-break"> -->

<div style="margin-top: 20px;">
	<table style= "table-container" style="margin-top: 20px;">
		<tr>
			<th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2"  >PTAA</th>
		</tr>
		<tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px; width: 200px;">Applicant</td>
		</tr>
		@if($ptaa_applications->count() > 0)
			@php $i = 1; @endphp
				@foreach($ptaa_applications as $ptaa)
					@if(in_array($ptaa->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
						<tr>
							<td style="font-size: 10px; padding: 5px;">{{$i}}</td>
							<td style="font-size: 10px; padding: 5px;">{{$ptaa->lastname}}, {{$ptaa->firstname}} {{$ptaa->middlename}}</td>
						</tr>
					@endif
					@php $i++; @endphp
				@endforeach
			@else
		@endif
	</table>
</div>
</div>

  </body>
</html>