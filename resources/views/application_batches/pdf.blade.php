<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>

	<style>
		table {
			border-collapse: collapse;
			width: 100%;
			table-layout: fixed;
		}

		table, td{
			border: 1px solid black;
		}

		td, th {
		padding: 8px;
		text-align: left;
		word-wrap: break-word;
		vertical-align: top;
  		border: 1px solid black;
		font-size: 7px;
	}

		.customer_type{
			font-size: 20px;
			font-weight: bold;
			color: blue;
		}
		

	</style>
  </head>
  <body>
	<h1>Submission List for {{$current_date}}</h1>
    <table>
	<tr>
		<td class="customer_type" style="text-align: center; font-size: 8px; width: 95%;">Walkin</td>
	</tr>
        <tr>
			<td class="customer_type" style="font-size: 8px; width: 5%;">No.</td>
			<td class="customer_type" style="font-size: 8px; width: 50%;">Applicant</td>
			<td class="customer_type" style="font-size: 8px; width: 40%;">Visa Type</td>
		</tr>
			@if($walkin_applications->count() > 0)
				@php $i = 1; @endphp
				@foreach($walkin_applications as $walkin)
					<tr>
						<td>{{$i}}</td>
						<td>{{$walkin->lastname}}, {{$walkin->firstname}} {{$walkin->middlename}}</td>
						<td>{{$walkin->visa_type}}</td>
					</tr>
					@php $i++; @endphp
				@endforeach
			@else
		<tr>
			<td colspan="3">-</td>
		</tr>
	@endif
	<tr>
		<td class="customer_type" style="text-align: center; font-size: 8px; width: 95%;">Corporate</td>
	</tr>
	<tr>
			<td class="customer_type" style="font-size: 8px; width: 5%;">No.</td>
			<td class="customer_type" style="font-size: 8px; width: 50%;">Applicant</td>
			<td class="customer_type" style="font-size: 8px; width: 40%;">Visa Type</td>
		</tr>
		@if($corporate_applications->count() > 0)
				@php $i = 1; @endphp
				@foreach($corporate_applications as $corporate)
					<tr>
						<td>{{$i}}</td>
						<td>{{$corporate->lastname}}, {{$corporate->firstname}} {{$corporate->middlename}}</td>
						<td>{{$corporate->visa_type}}</td>
					</tr>
					@php $i++; @endphp
				@endforeach
			@else			
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
		<td class="customer_type" style="text-align: center; font-size: 8px; width: 95%;">POEA</td>
	</tr>
        <tr>
			<td class="customer_type" style="font-size: 8px; width: 5%;">No.</td>
			<td class="customer_type" style="font-size: 8px; width: 50%;">Applicant</td>
			<td class="customer_type" style="font-size: 8px; width: 40%;">Visa Type</td>
		</tr>
		@if($poea_applications->count() > 0)
				@php $i = 1; @endphp
				@foreach($poea_applications as $poea)
					<tr>
						<td>{{$i}}</td>
						<td>{{$poea->lastname}}, {{$poea->firstname}} {{$poea->middlename}}</td>
						<td>{{$poea->visa_type}}</td>
					</tr>
					@php $i++; @endphp
				@endforeach
			@else	
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
		<td class="customer_type" style="text-align: center; font-size: 8px; width: 95%;">PIATA</td>
	</tr>
        <tr>
			<td class="customer_type" style="font-size: 8px; width: 5%;">No.</td>
			<td class="customer_type" style="font-size: 8px; width: 50%;">Applicant</td>
			<td class="customer_type" style="font-size: 8px; width: 40%;">Visa Type</td>
		</tr>
		@if($piata_applications->count() > 0)
				@php $i = 1; @endphp
				@foreach($piata_applications as $piata)
					<tr>
						<td>{{$i}}</td>
						<td>{{$piata->lastname}}, {{$piata->firstname}} {{$piata->middlename}}</td>
						<td>{{$piata->visa_type}}</td>
					</tr>
					@php $i++; @endphp
				@endforeach
			@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>ss
		<td class="customer_type" style="text-align: center; font-size: 8px; width: 95%;">PTAA</td>
	</tr>
        <tr>
			<td class="customer_type" style="font-size: 8px; width: 5%;">No.</td>
			<td class="customer_type" style="font-size: 8px; width: 50%;">Applicant</td>
			<td class="customer_type" style="font-size: 8px; width: 40%;">Visa Type</td>
		</tr>
		@if($ptaa_applications->count() > 0)
				@php $i = 1; @endphp
				@foreach($ptaa_applications as $ptaa)
					<tr>
						<td>{{$i}}</td>
						<td>{{$ptaa->lastname}}, {{$ptaa->firstname}} {{$ptaa->middlename}}</td>
						<td>{{$ptaa->visa_type}}</td>
					</tr>
					@php $i++; @endphp
				@endforeach
			@else			
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
    </table>
  </body>
</html>
