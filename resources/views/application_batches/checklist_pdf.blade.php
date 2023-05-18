<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>

	<style>
	td {
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

	.table-container:first-child {
	float: left;
	}

	.table-container:last-child {
	float: right;
	}

	.table-container {
	justify-content: space-between;
	width: 50%;
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


	<?php
    $walkin_applications_array = $walkin_applications->toArray();
    $walkinSplit = array_chunk($walkin_applications_array, ceil(count($walkin_applications_array) / 2));
	?>

<div style="display: flex; justify-content: space-between; overflow-x: auto;">
    <table class="table-container" style="margin-top: 20px;">
        <tr>
            <td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
            <td class="customer_type" style="font-size: 11px;">Walk-in Applicants</td>
        </tr>
        <?php $i = 1; ?>
        @foreach($walkinSplit[0] as $walkin)
            @if(in_array($walkin->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
                <tr>
                    <td style="font-size: 10px; padding: 5px; font-weight: bold; color: maroon;">{{$i}}</td>
                    <td style="font-size: 10px; padding: 5px;">{{$walkin->lastname}}, {{$walkin->firstname}} {{$walkin->middlename}}</td>
                </tr>
                <?php $i++; ?>
            @endif
        @endforeach
    </table>

    <table class="table-container" style="margin-top: 20px;">
        <tr>
            <td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
            <td class="customer_type" style="font-size: 11px;">Walk-in</td>
        </tr>
        <?php $i = count($walkinSplit[0]) + 1; ?>
        @foreach($walkinSplit[1] as $walkin)
            @if(in_array($walkin->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
                <tr>
                    <td style="font-size: 10px; padding: 5px; font-weight: bold; color: maroon;">{{$i}}</td>
                    <td style="font-size: 10px; padding: 5px;">{{$walkin->lastname}}, {{$walkin->firstname}} {{$walkin->middlename}}</td>
                </tr>
                <?php $i++; ?>
            @endif
        @endforeach
    </table>
</div>


<div class="page-break">
<?php
    $piata_applications_array = $piata_applications->toArray();

    // check if the array has any elements before using array_chunk()
    $piataSplit = count($piata_applications_array) > 0 ? array_chunk($piata_applications_array, ceil(count($piata_applications_array) / 2)) : [[]];
?>

<div style="margin-top: 20px;">
	<table class="table-container piata-table" style="margin-top: 20px;">
		<tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px;">PIATA</td>
		</tr>
        <?php $i = 1; ?>
        @foreach($piataSplit[0] as $piata)
            @if(in_array($piata->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
						<tr>
							<td style="font-size: 10px; padding: 5px;">{{$i}}</td>
							<td style="font-size: 10px; padding: 5px;">{{$piata->lastname}}, {{$piata->firstname}} {{$piata->middlename}}</td>
							</tr>
                <?php $i++; ?>
            @endif
        @endforeach
    </table>

	<table class="table-container" style="margin-top: 20px;">
        <tr>
            <td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
            <td class="customer_type" style="font-size: 11px;">PIATA</td>
        </tr>
        @foreach($piataSplit[1] as $piata)
			@if(in_array($piata->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]))
                <tr>
                    <td style="font-size: 10px; padding: 5px; font-weight: bold; color: maroon;">{{$i}}</td>
                    <td style="font-size: 10px; padding: 5px;">{{$piata->lastname}}, {{$piata->firstname}} {{$piata->middlename}}</td>
					</tr>
                <?php $i++; ?>
            @endif
        @endforeach
    </table>
</div>
</div>

<!-- <div style="margin-top: 20px;">
	<table style= "table-container">
		<tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px;">PTAA</td>
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

<div style="margin-top: 20px;">
	<table style= "table-container">	
		<tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px;">Corporate</td>
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

<div style="margin-top: 20px;">
	<table style= "table-container">
        <tr>
			<td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
			<td class="customer_type" style="font-size: 11px;">POEA</td>
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
</div> -->

  </body>
</html>