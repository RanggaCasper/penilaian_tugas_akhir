@extends('layouts.app')

@section('content')
@if ($data)
    @if ($data instanceof \App\Models\Thesis\Thesis)
        <x-card title="Pendaftaran Tugas Akhir - {{ $data->period->name }}">
            @if ($data->is_editable)
                <form action="{{ route('student.thesis.register.update') }}" data-reset="false" method="POST">  
                    @csrf  
                    @method('put')
                    <div class="mb-3">  
                        <x-input-field label="Judul Tugas Akhir" type="text" value="{{ $data->title }}" name="title" id="title" required />  
                    </div>  
                    <div class="mb-3">  
                        <x-input-field label="Tautan Dokumen" type="text" value="{{ $data->document }}" name="document" id="document" required />  
                    </div>  
                    <div class="mb-3">  
                        <x-input-field label="Tautan Dokumen Pendukung" type="text" value="{{ $data->support_document }}" name="support_document" id="support_document" />  
                    </div>
                    <div class="mb-3">
                        <label for="primary_mentor" class="form-label">Tipe</label>
                        <select name="rubric_id" class="form-select" id="rubric_id">
                            <option selected disabled>-- Pilih Tipe --</option>
                            @foreach (\App\Models\Rubric\Rubric::where('program_study_id', auth()->user()->program_study_id)->where('type', 'thesis')->get() as $item)
                                <option value="{{ $item->id }}"
                                    {{ $item->id == $data->rubric_id ? 'selected' : '' }}>
                                    {{ $item->name }}</option>
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
                    <x-input-field label="Judul Tugas Akhir" type="text" name="title" value="{{ $data->title }}" attr="disabled" />
                </div>
                <div class="mb-3">
                    <x-input-field label="Tautan Dokumen" type="text" name="document" value="{{ $data->document }}" attr="disabled" />
                </div>
                <div class="mb-3">
                    <x-input-field label="Tautan Dokumen Pendukung" type="text" name="support_document" value="{{ $data->support_document ?? '-' }}" attr="disabled" />
                </div>
                <div class="mb-3">
                    <x-input-field label="Tipe" type="text" name="rubric_id" value="{{ $data->rubric->name ?? '-' }}" attr="disabled" />
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
        <x-card title="Pendaftaran Tugas Akhir - {{ $data->name }}">  
            <form action="{{ route('student.thesis.register.store') }}" data-reset="false" method="POST">  
                @csrf  
                <div class="mb-3">  
                    <x-input-field label="Judul Tugas Akhir" type="text" value="{{ App\Models\Proposal\Proposal::where('student_id', auth()->user()->id)->first()->title ?? ''; }}" name="title" id="title" attr="readonly" />  
                </div>  
                <div class="mb-3">  
                    <x-input-field label="Tautan Dokumen" type="text" name="document" id="document" required />  
                </div>  
                <div class="mb-3">  
                    <x-input-field label="Tautan Dokumen Pendukung" type="text" name="support_document" id="support_document" />  
                </div>
                <div class="mb-3">
                    <label for="primary_mentor" class="form-label">Tipe</label>
                    <select name="rubric_id" class="form-select" id="rubric_id">
                        <option selected disabled>-- Pilih Tipe --</option>
                        @foreach (\App\Models\Rubric\Rubric::where('program_study_id', auth()->user()->program_study_id)->where('type', 'thesis')->get() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                Saat ini, pendaftaran Tugas Akhir belum tersedia untuk angkatan Anda.
            </p>
        </div>
    </div>
@endif 
@endsection
