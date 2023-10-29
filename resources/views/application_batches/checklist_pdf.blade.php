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
        column-gap: 20px; /* Adjust the gap between columns if needed */
    }

    .page-break {
        page-break-before: always;
    }

    .second-column {
		margin-top:-1000px;
        position: absolute;
        left: 50%;
        /* transform: translateX(50%); */
        margin-left: 15px;
    }


</style>
</head>

<body>
<div class="container" style="display:flex; align-items:center; text-align: center;">
    <div>
        <h1 style="margin: 0;">Checklist for {{$branch}}</h1>
        <h3 style="margin: 0;">{{$date}}</h3>
    </div>
    <img src="data:image/png;base64, {{base64_encode(QrCode::format('png')->errorCorrection('H')->generate($qrCodeContent)) }}"
         style="float: left; margin-right: 20px; width: 70px; height: 70px;">
</div>

<div class="table-container">
    <div style="margin-top: 20px; position: relative;">
        <table id="walkin-table">
            <tr>
                <th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2">Walk-in</th>
            </tr>
            <tr>
                <td class="customer_type" style="font-size: 11px; width: 25px;">No.</td>
                <td class="customer_type" style="font-size: 11px; width: 280px;">Applicant</td>
            </tr>
            @if($walkin_applications->count() > 0)
                @php $i = 1; @endphp
                @foreach($walkin_applications as $walkin)
                    @if(in_array($walkin->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]) && $walkin->branch == $branch)
                        @if($i % 37 === 1 && $i > 1)
                            </table>
                            <table class="second-column">
                                <tr>
                                    <th class="table-secondary" style="text-align:center; font-size:10px;" colspan="2">Walk-in</th>
                                </tr>
                                <tr>
                                    <td class="customer_type" style="font-size: 11px; width: 25px;">No.</td>
                                    <td class="customer_type" style="font-size: 11px; width: 280px;">Applicant</td>
                                </tr>
                        @endif
                        <tr>
                            <td style="font-size: 10px; padding: 5px;">{{$i}}</td>
                            <td style="font-size: 10px; padding: 5px;">{{$walkin->lastname}}, {{$walkin->firstname}} {{$walkin->middlename}}</td>
                        </tr>
                        @php $i++; @endphp
                    @endif
                @endforeach
            @endif
        </table>
    </div>
</div>
<div class="page-break">
    <div style="margin-top: 20px;">
        <table>
            <tr>
                <th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2">PIATA</th>
            </tr>
            <tr>
                <td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
                <td class="customer_type" style="font-size: 11px; width: 280px;">Applicant</td>
            </tr>
            @if($piata_applications->count() > 0)
                @php $i = 1; @endphp
                @foreach($piata_applications as $piata)
                    @if(in_array($piata->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]) && $piata->branch == $branch)
					@if($i % 37 === 1 && $i > 1)
                            </table>
                            <table class="second-column">
                                <tr>
                                    <th class="table-secondary" style="text-align:center; font-size:10px;" colspan="2">PIATA</th>
                                </tr>
                                <tr>
                                    <td class="customer_type" style="font-size: 11px; width: 25px;">No.</td>
                                    <td class="customer_type" style="font-size: 11px; width: 280px;">Applicant</td>
                                </tr>
                        @endif
                        <tr>
                            <td style="font-size: 10px; padding: 5px;">{{$i}}</td>
                            <td style="font-size: 10px; padding: 5px;">{{$piata->lastname}}, {{$piata->firstname}} {{$piata->middlename}}</td>
                        </tr>
                    @endif
                    @php $i++; @endphp
                @endforeach
            @endif
        </table>
    </div>
	<div class="page-break">
    <div style="margin-top: 20px;">
        <table>
            <tr>
                <th class="table-secondary" style="text-align:center; font-size:10px; " colspan="2">PTAA</th>
            </tr>
            <tr>
                <td class="customer_type" style="font-size: 11px; width: 5px;">No.</td>
                <td class="customer_type" style="font-size: 11px; width: 280px;">Applicant</td>
            </tr>
            @if($ptaa_applications->count() > 0)
                @php $i = 1; @endphp
                @foreach($ptaa_applications as $ptaa)
                    @if(in_array($ptaa->application_status, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]) && $ptaa->branch == $branch)
					@if($i % 37 === 1 && $i > 1)
                            </table>
                            <table class="second-column">
                                <tr>
                                    <th class="table-secondary" style="text-align:center; font-size:10px;" colspan="2">PTAA</th>
                                </tr>
                                <tr>
                                    <td class="customer_type" style="font-size: 11px; width: 25px;">No.</td>
                                    <td class="customer_type" style="font-size: 11px; width: 280px;">Applicant</td>
                                </tr>
                        @endif
                        <tr>
                            <td style="font-size: 10px; padding: 5px;">{{$i}}</td>
                            <td style="font-size: 10px; padding: 5px;">{{$ptaa->lastname}}, {{$ptaa->firstname}} {{$ptaa->middlename}}</td>
                        </tr>
                    @endif
                    @php $i++; @endphp
                @endforeach
            @endif
        </table>
    </div>
</div>

</body>
</html>

