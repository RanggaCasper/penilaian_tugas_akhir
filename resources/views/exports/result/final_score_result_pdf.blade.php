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
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Nilai Proposal (Rata-rata)</th>
                    <th>Nilai Pembimbing 1</th>
                    <th>Nilai Pembimbing 2</th>
                    <th>Nilai Tugas Akhir (Rata-rata)</th>
                    <th>Nilai Akhir Angka</th>
                    <th>Nilai Akhir Huruf</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item['student']['name'] ?? '-' }}</td>
                        <td>{{ $item['student']['identity'] ?? '-' }}</td>
                        <td>{{ $item['assessment']['proposal_score']['average_score'] ?? '-' }}</td>
                        <td>{{ $item['assessment']['guidance_score']['scores'][0]['score'] ?? '-' }}</td>
                        <td>{{ $item['assessment']['guidance_score']['scores'][1]['score'] ?? '-' }}</td>
                        <td>{{ $item['assessment']['thesis_score']['average_score'] ?? '-' }}</td>
                        <td>{{ $item['assessment']['total_score'] ?? '-' }}</td>
                        <td>{{ $convertToGrade($item['assessment']['total_score'] ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
