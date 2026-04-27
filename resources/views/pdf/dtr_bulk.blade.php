<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulk Daily Time Record</title>
    <style>
        .page-break {
            page-break-after: always;
        }
        .page-break:last-child {
            page-break-after: never;
        }
    </style>
</head>
<body>
    @foreach($usersData as $data)
        <div class="page-break">
            @include('pdf.dtr', [
                'user' => $data['user'],
                'monthName' => $data['monthName'],
                'year' => $data['year'],
                'dtrData' => $data['dtrData'],
                'total_undertime_hours' => $data['total_undertime_hours'],
                'total_undertime_minutes' => $data['total_undertime_minutes'],
                'total_days_logged' => $data['total_days_logged'],
            ])
        </div>
    @endforeach
</body>
</html>
