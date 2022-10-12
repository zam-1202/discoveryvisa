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
			font-weight: bold;
		}
		
		h1 {
			text-align: center;
		}
	</style>
  </head>
  <body>
	<h1>Checklist for {{$branch}} : {{$date}}</h1>
    <table>
		<tr>
			<td class="customer_type">No.</td>
			<td class="customer_type">Applicant</td>
			<td class="customer_type">Reference No</td>
		</tr>
		@if($walkin_applications->count() > 0)
			@php $i = 1; @endphp
			@foreach($walkin_applications as $walkin)
			<tr>
				<td>{{$i}}</td>
				<td>{{$walkin->lastname}}, {{$walkin->firstname}} {{$walkin->middlename}}</td>
				<td>{{$walkin->reference_no}}</td>
			</tr>
			@php $i++; @endphp
			@endforeach
		@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
			<td class="customer_type">No.</td>
			<td class="customer_type">PIATA</td>
			<td class="customer_type">Reference No</td>
		</tr>
		@if($piata_applications->count() > 0)
			@php $i = 1; @endphp
			@foreach($piata_applications as $piata)
			<tr>
				<td>{{$i}}</td>
				<td>{{$piata->lastname}}, {{$piata->firstname}} {{$piata->middlename}}</td>
				<td>{{$piata->reference_no}}</td>
			</tr>
			@php $i++; @endphp
			@endforeach
		@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
			<td class="customer_type">No.</td>
			<td class="customer_type">PTAA</td>
			<td class="customer_type">Reference No</td>
		</tr>
		@if($ptaa_applications->count() > 0)
			@php $i = 1; @endphp
			@foreach($ptaa_applications as $ptaa)
			<tr>
				<td>{{$i}}</td>
				<td>{{$ptaa->lastname}}, {{$ptaa->firstname}} {{$ptaa->middlename}}</td>
				<td>{{$ptaa->reference_no}}</td>
			</tr>
			@php $i++; @endphp
			@endforeach
		@else
			<tr>
				<td colspan="3">-</td>
			</tr>
		@endif
		<tr>
			<td class="customer_type">No.</td>
			<td class="customer_type">Corporate</td>
			<td class="customer_type">Reference No</td>
		</tr>
		@if($corporate_applications->count() > 0)
			@php $i = 1; @endphp
			@foreach($corporate_applications as $corporate)
			<tr>
				<td>{{$i}}</td>
				<td>{{$corporate->lastname}}, {{$corporate->firstname}} {{$corporate->middlename}}</td>
				<td>{{$corporate->reference_no}}</td>
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