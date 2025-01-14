<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Form Penilaian</title>  
    <style>  
        body {  
            font-family: Arial, sans-serif;  
            font-size: 12px;  
            margin: 20px;  
        }  
        h2 {  
            text-align: center;  
            margin-bottom: 10px;  
        }  
        p {  
            margin: 5px 0;  
        }  
        table {  
            width: 100%;  
            border-collapse: collapse;  
            margin-top: 10px;  
        }  
        th, td {  
            border: 1px solid black;  
            padding: 8px;  
            text-align: center;  
        }  
        th {  
            background-color: #f2f2f2;  
            font-size: 12px;  
        }  
        td {  
            font-size: 12px;  
        }  
        .text-left {  
            text-align: left;  
        }  
        .fw-bold {  
            font-weight: bold;  
        }  
        .text-center {  
            text-align: center;  
        }  
        .mt-2 {  
            margin-top: 10px;  
        }  
        .small-text {  
            font-size: 10px;  
        }  
    </style>  
</head>  
<body>  
    <h2>LEMBAR NILAI PEMBIMBING</h2>
    <div style="margin-bottom: 20px; font-size: 12px; display: flex; flex-direction: column;">  
        <div style="display: flex; margin-bottom: 5px;">  
            <span class="fw-bold" style="width: 30%; margin-right: 40px;">Nama</span>  
            <span style="width: 70%;">: {{ $data['student']['name'] }}</span>  
        </div>  
        <div style="display: flex; margin-bottom: 5px;">  
            <span class="fw-bold" style="width: 30%; margin-right: 47px;">NIM</span>  
            <span style="width: 70%;">   : {{ $data['student']['identity'] }}</span>  
        </div>  
        <div style="display: flex; margin-bottom: 5px;">  
            <span class="fw-bold" style="width: 30%;">Pembimbing</span>  
            <span style="width: 70%;">: {{ $data['examiner']['name'] }}</span>  
        </div> 
        <div style="display: flex; margin-bottom: 5px;">  
            <span class="fw-bold" style="width: 30%; margin-right: 40.5px;">Judul</span>  
            <span style="width: 70%;">: {{ $data['student']['proposal']['title'] }}</span>  
        </div> 
    </div>

    <table>
        <thead>  
            <tr> 
                <th scope="col">NO</th>  
                <th scope="col">UNSUR PENILAIAN</th>  
                <th scope="col">BOBOT</th>  
                <th scope="col">SKOR</th>  
                <th scope="col">BOBOT x SKOR</th>  
            </tr>  
        </thead>  
        <tbody>  
            @foreach ($data['scores'] as $index => $score)  
                <tr>  
                    <td>{{ $index + 1 }}</td>  
                    <td class="text-left">{{ $score['criteria']['name'] ?? '-' }}</td>  
                    <td>{{ $score['has_sub'] ? '-' : ($score['criteria']['weight'] . '%') }}</td>  
                    <td>{{ $score['score'] ?? '-' }}</td> 
                    <td>{{ $score['score'] && !$score['has_sub'] ? $score['score'] * ($score['criteria']['weight'] / 100) : '-' }}</td>  
                </tr>  
        
                @if (!empty($score['sub_scores']))  
                    @foreach ($score['sub_scores'] as $subScore)  
                        <tr>  
                            <td></td>  
                            <td class="text-left">&nbsp;&nbsp;- {{ $subScore['sub_criteria']['name'] ?? '-' }}</td>  
                            <td>{{ $subScore['sub_criteria']['weight'] ?? '-' }}%</td>  
                            <td>{{ $subScore['score'] ?? '-' }}</td>  
                            <td>{{ $subScore['score'] ? $subScore['score'] * ($subScore['sub_criteria']['weight'] / 100) : '-' }}</td>  
                        </tr>  
                    @endforeach  
                @endif  
            @endforeach  
            <tr>  
                <td colspan="4" class="text-center fw-bold">Jumlah Skor</td>  
                <td>  
                    {{  
                        array_reduce($data['scores'], function ($total, $score) {  
                            $mainScore = $score['score'] ? $score['score'] * ($score['criteria']['weight'] / 100) : 0;  
                            $subTotal = array_reduce($score['sub_scores'], function ($subTotal, $subScore) {  
                                return $subTotal + ($subScore['score'] * ($subScore['sub_criteria']['weight'] / 100) ?? 0);  
                            }, 0);  
                            return $total + $mainScore + $subTotal;  
                        }, 0)  
                    }}  
                </td>  
            </tr>  
            <tr>  
                <td colspan="5" class="fw-bold">Jumlah Skor (Huruf): {{ ucwords(\NumberFormatter::create('id_ID', \NumberFormatter::SPELLOUT)->format(  
                    array_reduce($data['scores'], function ($total, $score) {  
                        $mainScore = $score['score'] ? $score['score'] * ($score['criteria']['weight'] / 100) : 0;  
                        $subTotal = array_reduce($score['sub_scores'], function ($subTotal, $subScore) {  
                            return $subTotal + ($subScore['score'] * ($subScore['sub_criteria']['weight'] / 100) ?? 0);  
                        }, 0);  
                        return $total + $mainScore + $subTotal;  
                    }, 0)  
                )) }}</td>  
            </tr>  
        </tbody>  
    </table>  
</body>  
</html>