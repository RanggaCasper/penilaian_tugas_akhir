<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Revisi Ujian Komprehensif</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            font-size: 12px; /* Mengurangi ukuran font */
        }

        .container {
            width: 70%; /* Mengurangi lebar kontainer */
            margin: 20px auto;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 18px; /* Mengurangi ukuran font header */
            margin-top: 10px;
        }

        .subheader, .subheader2 {
            text-align: center;
            font-size: 14px; /* Mengurangi ukuran font subheader */
            margin-top: 5px;
        }

        .info {
            margin-top: 30px;
            font-size: 14px; /* Mengurangi ukuran font inputan */
        }

        .info div {
            margin-bottom: 10px;
        }

        .info label {
            display: inline-block;
            width: 150px;
        }

        .info input {
            border: none;
            border-bottom: 1px solid #000;
            width: 60%;
            padding: 3px;
            margin-left: 10px;
            font-size: 14px;
        }

        .table-container {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        .table-container th, .table-container td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
            font-size: 12px; /* Mengurangi ukuran font tabel */
        }

        .table-container th {
            background-color: #f2f2f2;
        }

        .table-container td {
            height: 40px;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
        }

        .footer .date {
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">LEMBAR PERBAIKAN</div>
        <div class="subheader">UJIAN KOMPREHENSIF</div>
        
        <div class="info">
            <div><label for="nama">Nama Mahasiswa</label>: <input type="text" id="nama" value="{{ $data['student']['name'] }}" name="nama"></div>
            <div><label for="nim">NIM</label>: <input type="text" id="nim" value="{{ $data['student']['identity'] }}" name="nim"></div>
            <div><label for="prodi">Program Studi</label>: <input type="text" id="prodi" value="{{ $data['student']['program_study']['name'] }}" name="prodi"></div>
            <div><label for="judul">Judul Skripsi</label>: <input type="text" id="judul" value="{{ $data['student']['proposal']['title'] }}" name="judul"></div>
        </div>
        
        <table class="table-container">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>CATATAN PERBAIKAN</th>
                    <th>BAB</th>
                    <th>HALAMAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['revisions'] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['description'] }}</td>
                        <td>{{ $item['chapter'] }}</td>
                        <td>{{ $item['page'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
