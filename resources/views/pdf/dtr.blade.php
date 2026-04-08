<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Time Record</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-size: 9px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 30px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            border-top: 1px solid black;
            width: 200px;
            text-align: center;
            padding-top: 5px;
            display: inline-block;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1000;
        }
    </style>
</head>
<body>
    <div class="watermark uppercase">Official DTR</div>

    <div class="header">
        <h1>Daily Time Record</h1>
        <p>Civil Service Form No. 48</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">NAME:</span> {{ $user->Name }}
        </div>
        <div class="info-row">
            <span class="label">MONTH:</span> {{ $monthName }} {{ $year }}
        </div>
        <div class="info-row">
            <span class="label">OFFICE:</span> {{ $user->dept->DeptName ?? 'N/A' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Day</th>
                <th colspan="2">AM</th>
                <th colspan="2">PM</th>
                <th colspan="2">Underline</th>
            </tr>
            <tr>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Hours</th>
                <th>Minutes</th>
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

    <div class="footer">
        <p>I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.</p>
        
        <div class="signature-section">
            <div style="margin-bottom: 40px;">
                <div class="signature-line" style="width: 250px;">
                    Employee Signature
                </div>
            </div>
            
            <p>Verified as to the prescribed office hours:</p>
            
            <div class="signature-line" style="width: 250px;">
                In Charge
            </div>
        </div>
    </div>
</body>
</html>
