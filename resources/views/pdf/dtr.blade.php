<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Time Record</title>
    <style>
        @page { margin: 15px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .dtr-container {
            width: 48%; /* Maintains the half-page width */
            padding: 10px;
            position: relative;
        }
        .page-break {
            page-break-after: always;
        }
        .form-label {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 13px;
            margin: 0 0 5px 0;
            font-weight: normal;
        }
        .header .name {
            font-size: 13px;
            text-transform: uppercase;
            margin: 0;
        }
        .header .name-line {
            border-top: 1px solid black;
            width: 90%;
            margin: 0 auto;
        }
        .header .name-label {
            font-size: 11px;
            margin-top: 2px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .info-table td {
            border: none;
            padding: 3px 0;
            text-align: left;
        }
        table.dtr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.dtr-table th, table.dtr-table td {
            border: 1px solid black;
            padding: 3px 2px;
            text-align: center;
        }
        table.dtr-table th {
            font-weight: normal;
        }
        .certify {
            font-size: 10px;
            text-align: left;
            margin-top: 15px;
        }
        .signatures {
            margin-top: 30px;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
        }
        .sig-label {
            font-size: 10px;
            margin-top: 2px;
            margin-bottom: 25px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 40px;
            color: rgba(0, 0, 0, 0.03);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    @for ($i = 0; $i < 2; $i++)
    <div class="dtr-container{{ $i === 0 ? ' page-break' : '' }}">
        <div class="watermark">OFFICIAL COPY</div>
        
        <div class="form-label">Form 48</div>
        <div class="header">
            <h1>DAILY TIME RECORD</h1>
            <div class="name">{{ $user->Name }}</div>
            <div class="name-line"></div>
            <div class="name-label">Name</div>
        </div>

        @php 
            $startDate = \Carbon\Carbon::parse("1 $monthName $year");
            $endDate = $startDate->copy()->endOfMonth();
        @endphp

        <table class="info-table">
            <tr>
                <td style="width: 60px;">Pay Perio</td>
                <td>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td>Schedule:</td>
                <td></td>
            </tr>
        </table>

        @php
            $formatTime = function($time) {
                if (!$time) return '';
                $parsed = \Carbon\Carbon::parse($time);
                return $parsed->format('h:i') . substr($parsed->format('a'), 0, 1);
            };
        @endphp

        <table class="dtr-table">
            <thead>
                <tr>
                    <th rowspan="2">Day</th>
                    <th colspan="2">AM</th>
                    <th colspan="2">PM</th>
                    <th colspan="2">Undertime</th>
                </tr>
                <tr>
                    <th>Arrive</th>
                    <th>Depart</th>
                    <th>Arrive</th>
                    <th>Depart</th>
                    <th>Hours</th>
                    <th>Minutes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dtrData as $day => $data)
                @php
                    $currentDate = \Carbon\Carbon::parse("$day $monthName $year");
                    $dayStr = $currentDate->format('m d') . ' ' . substr($currentDate->format('D'), 0, 2);
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $dayStr }}</td>
                    <td>{{ $formatTime($data['am_in']) }}</td>
                    <td>{{ $formatTime($data['am_out']) }}</td>
                    <td>{{ $formatTime($data['pm_in']) }}</td>
                    <td>{{ $formatTime($data['pm_out']) }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="5" style="text-align: left;">Days: </td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="certify">
            I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
        </div>

        <div class="signatures">
            <div class="sig-line"></div>
            <div class="sig-label">(Signature)</div>
            
            <div class="sig-line"></div>
            <div class="sig-label">VERIFIED as to the prescribed office hours</div>

            <div class="sig-line"></div>
            <div class="sig-label">Division Chief / Head of Section</div>
        </div>
    </div>
    @endfor
</body>
</html>
