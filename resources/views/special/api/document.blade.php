@extends('layouts.app')

@section('content')
<h5 class="mb-3">Proposal (POST)</h5>
<x-card title="Request">
    <ul class="mb-3 nav nav-tabs nav-border-top nav-border-top-primary" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#endpoint" role="tab" aria-selected="true">
                Endpoint
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#header" role="tab" aria-selected="false" tabindex="-1">
                Header
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#body" role="tab" aria-selected="false" tabindex="-1">
                Body
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#request" role="tab" aria-selected="false" tabindex="-1">
                Contoh Request
            </a>
        </li>
    </ul>
    <div class="tab-content text-muted">
        <div class="tab-pane active show" id="endpoint" role="tabpanel">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <span class="badge bg-primary">POST</span>
                </div>
                <div class="flex-grow-1 ms-2">
                <a href="{{ route('api.proposal.get') }}">{{ route('api.proposal.get') }}</a>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="header" role="tabpanel">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Key</td>
                        <td>{api_key}</td>
                        <td>Ganti {api_key} dengan API Key Anda</td>
                    </tr>
                    <tr>
                        <td>Signature</td>
                        <td>{api_id}:{api_key}</td>
                        <td>Signature dengan formula md5 dari {api_id}:{api_key}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="body" role="tabpanel">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Wajib</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>NIM</td>
                        <td>{nim}</td>
                        <td>Tidak</td>
                        <td>Masukan NIM untuk mengambil data</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="request" role="tabpanel"><pre><code data-language="php">
&lt;?php
// Endpoint API
$apiUrl = '{{ route('api.proposal.get') }}';

// Kredensial API
$apiId = '{{ $data->api_id }}'; // Api Id Anda
$apiKey = '{{ $data->api_key }}'; // Api Key Anda

// Buat Signature
$signature = md5($apiId . ':' . $apiKey);

// Nim yang ingin difilter
$nim = '2215354079'; // Ganti dengan nim yang diinginkan

// Data yang akan dikirimkan dalam body
$requestData = [
    'nim' => $nim
];

// Inisialisasi cURL
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'key: ' . $apiKey,                          // Tambahkan API key ke header
        'signature: ' . $signature                  // Tambahkan signature ke header
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestData), // Kirimkan nim dalam body JSON (Hapus jika tidak dibutuhkan)
    CURLOPT_FAILONERROR => true
]);

// Eksekusi cURL
$response = curl_exec($curl);
$error = curl_error($curl);

// Tutup cURL
curl_close($curl);

// Tangani respons atau error
if ($error) {
    echo "Kesalahan cURL: " . $error; // Tampilkan pesan error jika ada
} else {
    echo "Respons API: " . $response; // Tampilkan respons API jika berhasil
}
?&gt;
            </code></pre>
                
        </div>
        <div class="tab-pane" id="nav-border-top-settings" role="tabpanel">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <i class="ri-checkbox-circle-line text-success"></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    when darkness overspreads my eyes, and heaven and earth seem to dwell in my soul and absorb its power, like the form of a beloved mistress, then I often think with longing, Oh, would I could describe these conceptions, could impress upon paper all that is living so full and warm within me, that it might be the.
                    <div class="mt-2">
                        <a href="javascript:void(0);" class="btn btn-link">Read More <i class="align-middle ri-arrow-right-line ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-card>

<x-card title="Response">
    <h6>Response Sukses (200 OK) - Tanpa Fillter</h6>
    <pre><code data-language="json">
{
    "status": true,
    "data": [
        {
            "id": 1,
            "nim": "2215354059",
            "status": false
        },
        {
            "id": 2,
            "nim": "2215354071",
            "status": true
        },
        {
            "id": 3,
            "nim": "2215354079",
            "status": true
        },
        {
            "id": 4,
            "nim": "2215354091",
            "status": false
        }
    ]
}

</code></pre>

<h6>Response Sukses (200 OK) - Dengan Fillter NIM</h6>
    <pre><code data-language="json">
{
    "status": true,
    "data": [
        {
            "id": 3,
            "nim": "2215354079",
            "status": true
        }
    ]
}

</code></pre>

<h6>Response Gagal (401 Unauthorized)</h6>
    <pre><code data-language="json">
{
    "status": false,
    "message": "Unauthorized"
}

</code></pre>

<h6>Response Gagal (403 Forbidden)</h6>
    <pre><code data-language="json">
{
    "status": false,
    "message": "Access denied: IP 127.0.0.1 not allowed!"
}

</code></pre>

<h6>Response Gagal (500 Internal Server Error)</h6>
    <pre><code data-language="json">
{
    "status": false,
    "message": "An error occurred while processing the request."
}

</code></pre>
</x-card>
@endsection

@push('styles')
<link href="{{ asset('assets/rainbow/css/monokai.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('assets/js/rainbow-custom.min.js') }}"></script>
@endpush