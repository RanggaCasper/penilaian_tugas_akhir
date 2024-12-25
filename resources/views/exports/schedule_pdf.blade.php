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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
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
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Mahasiswa</th>
                <th>Penguji 1</th>
                <th>Penguji 2</th>
                <th>Penguji 3</th>
                <th>Tanggal Ujian</th>
                <th>Waktu Mulai</th>
                <th>Waktu Berakhir</th>
                <th>Ruangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $schedule)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $schedule->student->nim }}</td>
                    <td>{{ $schedule->student->name }}</td>
                    <td>{{ $schedule->primary_examiner->name ?? '-' }}</td>
                    <td>{{ $schedule->secondary_examiner->name ?? '-' }}</td>
                    <td>{{ $schedule->tertiary_examiner->name ?? '-' }}</td>
                    <td>{{ $schedule->exam_date }}</td>
                    <td>{{ $schedule->start_time }}</td>
                    <td>{{ $schedule->end_time }}</td>
                    <td>{{ $schedule->room }}</td>
                    <td>{{ $schedule->status ? 'Active' : 'Locked' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>