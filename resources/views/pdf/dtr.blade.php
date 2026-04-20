<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Time Record</title>
    <style>
        @page { margin: 10px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-between;
        }
        .dtr-container {
            width: 48%; /* Roughly half the page */
            padding: 10px;
            display: inline-block;
            vertical-align: top;
            border-right: 1px dashed #ccc;
        }
        .dtr-container:last-child {
            border-right: none;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        .header h1 {
            font-size: 12px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 1px 0;
            font-size: 9px;
        }
        .info-section {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 2px;
        }
        .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: normal;
        }
        .certify {
            font-size: 8px;
            text-align: justify;
            margin-top: 10px;
            font-style: italic;
        }
        .signatures {
            margin-top: 20px;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 15px auto 0;
            padding-top: 2px;
            font-size: 9px;
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
    <div class="dtr-container">
        <div class="watermark">OFFICIAL COPY</div>
        
        <div class="header">
            <p>Civil Service Form No. 48</p>
            <h1>DAILY TIME RECORD</h1>
            <p><strong>{{ $user->Name }}</strong></p>
            <p>(Name)</p>
        </div>

        <div class="info-section">
            <div class="info-row">
                For the month of: <u>{{ $monthName }} {{ $year }}</u>
            </div>
            <div class="info-row">
                Official hours for arrival <br> and departure: <u>Reg. Day: 8:00-5:00</u>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">Day</th>
                    <th colspan="2">A.M.</th>
                    <th colspan="2">P.M.</th>
                    <th colspan="2">Undertime</th>
                </tr>
                <tr>
                    <th>In</th>
                    <th>Out</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Hrs</th>
                    <th>Min</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dtrData as $day => $data)
                <tr>
                    <td>{{ $day }}</td>
                    <td>{{ $data['am_in'] ?? '' }}</td>
                    <td>{{ $data['am_out'] ?? '' }}</td>
                    <td>{{ $data['pm_in'] ?? '' }}</td>
                    <td>{{ $data['pm_out'] ?? '' }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="certify">
            I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
        </div>

        <div class="signatures">
            <div class="sig-line">Employee's Signature</div>
            <p style="margin-top: 10px; font-size: 8px;">Verified as to the prescribed office hours:</p>
            <div class="sig-line">In Charge</div>
        </div>
    </div>
    @endfor
</body>
</html>
