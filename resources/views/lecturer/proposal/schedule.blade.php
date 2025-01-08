@extends('layouts.app')

@section('title', 'Jadwal Ujian Proposal')

@section('content')
<x-card title="Data Jadwal Ujian Proposal">   
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
        Download
    </button>
    <table id="datatables" class="table align-middle nowrap">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam Mulai</th>
                <th>Jam Berakhir</th>
                <th>Mahasiswa</th>
                <th>Posisi</th>
                <th>Status</th>
            </tr>
        </thead>
    </table>
</x-card>

<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Tanggal Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="exam_date_fillter" class="form-label">Pilih Tanggal Ujian</label>
                        <input type="date" class="form-control" id="exam_date_fillter" name="exam_date_fillter" required>
                    </div>
                    <button type="button" id="downloadExcel" class="btn btn-primary">Excel</button>
                    <button type="button" id="downloadPDF" class="btn btn-primary">PDF</button>
                </form>
            </div>
        </div>
    </div>
</div> 
@endsection

@push('scripts')
<script>
    $('#downloadExcel').on('click', function () {
        downloadFile('excel');
    });

    $('#downloadPDF').on('click', function () {
        downloadFile('pdf');
    });
    
    function downloadFile(format) {
        const examDate = $('#exam_date_fillter').val();

        if (examDate) {
            const fileType = format === 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf';
            const fileExtension = format === 'excel' ? 'xlsx' : 'pdf';

            $.ajax({
                url: '{{ route("lecturer.proposal.schedule.get") }}',
                type: 'GET',
                data: {
                    export: format,
                    exam_date: examDate
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response, status, xhr) {
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    let filename = 'jadwal_ujian_' + examDate + '.' + fileExtension;
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    const blob = new Blob([response], { type: fileType });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Terjadi Kesalahan !</h4><p class="mx-4 mb-0 text-muted">Gagal mendownload jadwal ujian.</p></div></div>',
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
        } else {
            Swal.fire({
                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Terjadi Kesalahan !</h4><p class="mx-4 mb-0 text-muted">Silahkan masukan tanggal ujian.</p></div></div>',
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
    }

    $('#datatables').DataTable({
        processing: true,
        serverSide: false,
        scrollX: true,
        ajax: '{{ route('lecturer.proposal.schedule.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'exam_date', name: 'exam_date' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'end_time' },
            { data: 'student.name', name: 'student.name' },
            { data: 'position', name: 'position' },
            { data: 'status', name: 'status' },
        ],
    });
</script>
@endpush