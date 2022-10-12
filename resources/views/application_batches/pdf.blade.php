<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
	
	<style>
		table {
			border-collapse: collapse;
			width: 100%;
		}
		
		table, td{
			border: 1px solid black;
		}
		
		td {
			padding: 10px;
			text-align: center;
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
			<td colspan="3" class="customer_type">Walk-In</td>
		</tr>
		@if($walkin_applications->count() > 0)
			@foreach($walkin_applications as $walkin)
			<tr>
				<td>{{$walkin->reference_no}}</td>
				<td>{{$walkin->lastname}}, {{$walkin->firstname}} {{$walkin->middlename}}</td>
				<td>Walk-In</td>
			</tr>
			@endforeach
		@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
			<td colspan="3" class="customer_type">PIATA</td>
		</tr>
		@if($piata_applications->count() > 0)
			@foreach($piata_applications as $piata)
			<tr>
				<td>{{$piata->reference_no}}</td>
				<td>{{$piata->lastname}}, {{$piata->firstname}} {{$piata->middlename}}</td>
				<td>{{$partner_companies_array[$piata->customer_company]}}</td>
			</tr>
			@endforeach
		@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
			<td colspan="3" class="customer_type">PTAA</td>
		</tr>
		@if($ptaa_applications->count() > 0)
			@foreach($ptaa_applications as $ptaa)
			<tr>
				<td>{{$ptaa->reference_no}}</td>
				<td>{{$ptaa->lastname}}, {{$ptaa->firstname}} {{$ptaa->middlename}}</td>
				<td>{{$partner_companies_array[$ptaa->customer_company]}}</td>
			</tr>
			@endforeach
		@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
			<td colspan="3" class="customer_type">Corporate</td>
		</tr>
		@if($corporate_applications->count() > 0)
			@foreach($corporate_applications as $corporate)
			<tr>
				<td>{{$corporate->reference_no}}</td>
				<td>{{$corporate->lastname}}, {{$corporate->firstname}} {{$corporate->middlename}}</td>
				<td>{{$partner_companies_array[$corporate->customer_company]}}</td>
			</tr>
			@endforeach
		@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
    </table>
  </body>
</html>