<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            table-layout: auto;
        }
        th, td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #008000;
            color: #ffffff;
            font-size: 10px;
        }
        td {
            background-color: #f5f5f5;
            font-size: 9px;
        }
        
        @media (max-width: 768px) {
            body {
                font-size: 9px;
            }
            th, td {
                font-size: 8px;
                padding: 3px;
            }
        }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Mahasiswa</th>
                    <th>Judul</th>
                    <th>Penguji 1</th>
                    <th>Penguji 2</th>
                    <th>Penguji 3</th>
                    <th>Waktu</th>
                    <th>Ruangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $schedule)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $schedule->student->identity }}</td>
                        <td>{{ $schedule->student->name }}</td>
                        <td>{{ $schedule->student->thesis->title ?? '-' }}</td>
                        <td>{{ $schedule->primary_examiner->name ?? '-' }}</td>
                        <td>{{ $schedule->secondary_examiner->name ?? '-' }}</td>
                        <td>{{ $schedule->tertiary_examiner->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        <td>{{ $schedule->room }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
