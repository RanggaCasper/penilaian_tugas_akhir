@extends('layouts.app')

@section('content')
<x-card title="Jadwal Ujian">
    <div class="row">
        <div class="col-lg-6">
            <div class="mb-3">
                <x-input-field 
                    label="Tanggal Ujian" 
                    type="text" 
                    name="exam_date" 
                    id="exam_date" 
                    value="{{ $schedule && $schedule->exam_date ? \Carbon\Carbon::parse($schedule->exam_date)->translatedFormat('j F Y') : 'Tidak Ada' }}"
                    attr="disabled" 
                />
            </div>
            <div class="mb-3">
                <x-input-field 
                    label="Waktu" 
                    type="text" 
                    name="start_time" 
                    id="start_time" 
                    value="{{ $schedule && $schedule->start_time && $schedule->end_time 
                    ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($schedule->end_time)->format('H:i') 
                    : 'Tidak Ada' }}"      
                    attr="disabled" 
                />
            </div>
            <div class="mb-3">
                <x-input-field 
                    label="Ruangan" 
                    type="text" 
                    name="room" 
                    id="room" 
                    value="{{ $schedule->room ?? 'Tidak Ada' }}" 
                    attr="disabled" 
                />
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-3">
                <x-input-field 
                    label="Penguji 1" 
                    type="text" 
                    name="primary_examiner" 
                    id="primary_examiner" 
                    value="{{ $schedule->primary_examiner->name ?? 'Tidak Ada' }}" 
                    attr="disabled" 
                />
            </div>
            <div class="mb-3">
                <x-input-field 
                    label="Penguji 2" 
                    type="text" 
                    name="secondary_examiner" 
                    id="secondary_examiner" 
                    value="{{ $schedule->secondary_examiner->name ?? 'Tidak Ada' }}" 
                    attr="disabled" 
                />
            </div>
            <div class="mb-3">
                <x-input-field 
                    label="Penguji 3" 
                    type="text" 
                    name="tertiary_examiner" 
                    id="tertiary_examiner" 
                    value="{{ $schedule->tertiary_examiner->name ?? 'Tidak Ada' }}" 
                    attr="disabled" 
                />
            </div>
        </div>
    </div>
    
    <button id="download-schedule" class="btn btn-primary">Download</button>
</x-card>
@endsection

@push('scripts')
    <script>
        $('#download-schedule').on('click', function () {
            $.ajax({
                url: '{{ route("student.final_project.schedule.download") }}',
                type: 'GET',
                success: function (response) {
                    const link = document.createElement('a');
                    link.href = '{{ route('student.final_project.schedule.download') }}';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                },
                error: function (error) {
                    Swal.fire({
                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Terjadi Kesalahan !</h4><p class="mx-4 mb-0 text-muted">' +error.responseJSON.message+ '</p></div></div>',
                        showCancelButton: !0,
                        showConfirmButton: !1,
                        customClass: {
                            cancelButton: "btn btn-primary w-xs mb-1"
                        },
                        cancelButtonText: "Back",
                        buttonsStyling: !1,
                        showCloseButton: !0
                    });
                }
            });
        });

    </script>
@endpush