@extends('layouts.app')

@section('content')
@if ($data)
    @if ($data instanceof \App\Models\Proposal\Proposal)
        <x-card title="Pendaftaran Proposal - {{ $data->period->name }}">
            @if ($data->is_editable)
                <form action="{{ route('student.proposal.register.update') }}" data-reset="false" method="POST">  
                    @csrf  
                    @method('put')
                    <div class="mb-3">  
                        <x-input-field label="Judul Proposal" type="text" value="{{ $data->title }}" name="title" id="title" required />  
                    </div>  
                    <div class="mb-3">  
                        <x-input-field label="Tautan Dokumen" type="text" value="{{ $data->document }}" name="document" id="document" required />  
                    </div>  
                    <div class="mb-3">  
                        <x-input-field label="Tautan Dokumen Pendukung" type="text" value="{{ $data->support_document }}" name="support_document" id="support_document" />  
                    </div>
                    <div class="mb-3">
                        <label for="primary_mentor" class="form-label">Pembimbing 1</label>
                        <select id="primary_mentor" name="primary_mentor_id" class="form-control select2">
                            <option selected disabled>-- Pilih Pembimbing --</option>
                            @foreach (\App\Models\User::where('role_id', 3)->get() as $item)
                                <option value="{{ $item->id }}" 
                                    {{ $item->id == $data->primary_mentor_id ? 'selected' : '' }}>
                                    {{ $item->name . ' - ' . $item->secondary_identity }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="buttons">
                        <x-button type="submit" class="btn btn-primary" label="Submit" />  
                        <x-button type="reset" class="btn btn-danger" label="Reset" />   
                    </div>
                </form> 
            @else
                <div class="mb-3 text-white alert alert-success alert-dismissible bg-success alert-label-icon fade show material-shadow" role="alert">
                    <i class="ri-check-line label-icon"></i><strong>Berhasil</strong> - Anda berhasil mendaftar. Jika ingin memperbaharui data, hubungi administrator.
                </div>
                <div class="mb-3">
                    <x-input-field label="Judul Proposal" type="text" name="title" value="{{ $data->title }}" attr="disabled" />
                </div>
                <div class="mb-3">
                    <x-input-field label="Tautan Dokumen" type="text" name="document" value="{{ $data->document }}" attr="disabled" />
                </div>
                <div class="mb-3">
                    <x-input-field label="Tautan Dokumen Pendukung" type="text" name="support_document" value="{{ $data->support_document ?? '-' }}" attr="disabled" />
                </div>
                <div class="mb-3">
                    <x-input-field label="Pembimbing 1" type="text" name="primary_mentor_id" value="{{ $data->primary_mentor->name ?? '-' }}" attr="disabled" />
                </div>
                <div id="buttons">
                    <p class="p-0 mb-2">Status</p>
                    @php
                        $statusClass = match(strtolower($data->status)) {
                            'menunggu' => 'btn-warning',
                            'disetujui' => 'btn-success',
                            'ditolak    ' => 'btn-danger',
                            default => 'btn-secondary',
                        };
                    @endphp

                    <span class="btn btn-sm rounded-pill {{ $statusClass }}" style="padding: 5px 1rem; cursor: default;">
                        {{ ucfirst($data->status) }}
                    </span>
                </div>
            @endif
        </x-card>
    @else
        <x-card title="Pendaftaran Proposal - {{ $data->name }}">  
            <form action="{{ route('student.proposal.register.store') }}" data-reset="false" method="POST">  
                @csrf  
                <div class="mb-3">  
                    <x-input-field label="Judul Proposal" type="text" name="title" id="title" required />  
                </div>  
                <div class="mb-3">  
                    <x-input-field label="Tautan Dokumen" type="text" name="document" id="document" required />  
                </div>  
                <div class="mb-3">  
                    <x-input-field label="Tautan Dokumen Pendukung" type="text" name="support_document" id="support_document" />  
                </div>
                <div class="mb-3">
                    <label for="primary_mentor" class="form-label">Pembimbing 1</label>
                    <select id="primary_mentor" name="primary_mentor_id" class="form-control select2" >
                        <option selected disabled>-- Pilih Pembimbing --</option>
                        @foreach (\App\Models\User::where('role_id', 3)->get() as $item)
                            <option value="{{ $item->id }}">{{ $item->name . ' - ' . $item->secondary_identity }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="buttons">
                    <x-button type="submit" class="btn btn-primary" label="Submit" />  
                    <x-button type="reset" class="btn btn-danger" label="Reset" />   
                </div>
            </form>  
        </x-card>
    @endif
@else
    <div class="text-center d-flex flex-column justify-content-center align-items-center">
        <div>
            <img src="{{ asset('assets/undraw/no-data.svg') }}" class="img-fluid" width="50%" height="50%" alt="No Data">
        </div>
        <div>
            <p class="mt-3">
                Saat ini, Pendaftaran proposal belum tersedia untuk angkatan Anda.
            </p>
        </div>
    </div>
@endif 
@endsection

@push('scripts')
<script>
    $('#primary_mentor').select2({
        placeholder: "-- Pilih Pembimbing --",
        allowClear: true
    });
</script>
@endpush
