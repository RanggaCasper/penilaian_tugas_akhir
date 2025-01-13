@extends('layouts.app')

@section('title', 'Jadwal Ujian Tugas Akhir')

@section('content')
    <x-card title="Tambah Jadwal Ujian">
        <form action="{{ route('admin.thesis.schedule.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <x-input-field label="Tanggal Ujian" type="date" name="exam_date" id="exam_date" />
                    </div>
                    <div class="mb-3">
                        <x-input-field label="Jam Mulai" type="time" name="start_time" id="start_time" />
                    </div>
                    <div class="mb-3">
                        <x-input-field label="Ruangan" type="text" name="room" id="room" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="student" class="form-label">Mahasiswa</label>
                        <select id="student" name="student_id" class="form-control select2" 
                                data-placeholder="-- Pilih Mahasiswa --" 
                                data-ajax-url="{{ route('admin.thesis.schedule.getStudent') }}" 
                                data-type="student"></select>
                    </div>
                    <div class="mb-3">
                        <label for="examiner-1" class="form-label">Penguji 1</label>
                        <select id="examiner-1" name="primary_examiner_id" class="form-control select2" 
                                data-placeholder="-- Pilih Penguji --" 
                                data-ajax-url="{{ route('admin.thesis.schedule.getExaminer') }}" 
                                data-type="examiner"></select>
                    </div>
                    <div class="mb-3">
                        <label for="examiner-2" class="form-label">Penguji 2</label>
                        <select id="examiner-2" name="secondary_examiner_id" class="form-control select2" 
                                data-placeholder="-- Pilih Penguji --" 
                                data-ajax-url="{{ route('admin.thesis.schedule.getExaminer') }}" 
                                data-type="examiner"></select>
                    </div>
                    <div class="mb-3">
                        <label for="examiner-3" class="form-label">Penguji 3</label>
                        <select id="examiner-3" name="tertiary_examiner_id" class="form-control select2" 
                                data-placeholder="-- Pilih Penguji --" 
                                data-ajax-url="{{ route('admin.thesis.schedule.getExaminer') }}" 
                                data-type="examiner"></select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="is_editable_checkbox">Status</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" name="is_editable" type="checkbox" role="switch" id="is_editable_checkbox" checked>
                    <label class="form-check-label" for="is_editable_checkbox">Aktif</label>
                </div>
            </div>
            <x-button type="submit" class="btn btn-primary" label="Submit" />
            <x-button type="reset" class="btn btn-danger" label="Reset" />
        </form>
    </x-card>
    
    <x-card title="Data Jadwal Ujian">   
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
                    <th>Judul TA</th>
                    <th>Status</th>
                    <th>Aksi</th>
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

    <div class="modal fade" id="modal" aria-labelledby="updateScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateScheduleModalLabel">Edit Jadwal Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="form_update">
                        @csrf
                        @method("PUT")
                        <div class="mb-3">
                            <x-input-field label="Tanggal Pengujian" type="date" name="exam_date" id="exam_date_update" />
                        </div>
                        <div class="mb-3">
                            <x-input-field label="Jam Mulai" type="time" name="start_time" id="start_time_update" />
                        </div>
                        <div class="mb-3">
                            <x-input-field label="Jam Berakhir" type="time" name="end_time" id="end_time_update" />
                        </div>
                        <div class="mb-3">
                            <x-input-field label="Ruangan" type="text" name="room" id="room_update" />
                        </div>
                        <div class="mb-3">
                            <label for="student_update" class="form-label">Mahasiswa</label>
                            <select id="student_update" name="student_id" class="form-control select2" 
                                    data-placeholder="-- Pilih Mahasiswa --" 
                                    data-ajax-url="{{ route('admin.thesis.schedule.getStudent') }}" 
                                    data-type="student"></select>
                        </div>
                        <div class="mb-3">
                            <label for="examiner-1_update" class="form-label">Penguji 1</label>
                            <select id="examiner-1_update" name="primary_examiner_id" class="form-control select2" 
                                    data-placeholder="-- Pilih Penguji --" 
                                    data-ajax-url="{{ route('admin.thesis.schedule.getExaminer') }}" 
                                    data-type="examiner"></select>
                        </div>
                        <div class="mb-3">
                            <label for="examiner-2_update" class="form-label">Penguji 2</label>
                            <select id="examiner-2_update" name="secondary_examiner_id" class="form-control select2" 
                                    data-placeholder="-- Pilih Penguji --" 
                                    data-ajax-url="{{ route('admin.thesis.schedule.getExaminer') }}" 
                                    data-type="examiner"></select>
                        </div>
                        <div class="mb-3">
                            <label for="examiner-3_update" class="form-label">Penguji 3</label>
                            <select id="examiner-3_update" name="tertiary_examiner_id" class="form-control select2" 
                                    data-placeholder="-- Pilih Penguji --" 
                                    data-ajax-url="{{ route('admin.thesis.schedule.getExaminer') }}" 
                                    data-type="examiner"></select>
                        </div>
                        <div class="mb-3">
                            <label for="is_editable_checkbox">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="is_editable" type="checkbox" role="switch" id="is_editable_checkbox_update" checked>
                                <label class="form-check-label" for="is_editable_checkbox">Aktif</label>
                            </div>
                        </div>
                        
                        <x-button type="submit" class="btn btn-primary" label="Submit" />
                        <x-button type="reset" class="btn btn-danger" label="Reset" />
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
                url: '{{ route("admin.thesis.schedule.get") }}',
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

    $('.select2').each(function () {
        var $p = $(this).parent();
        var type = $(this).data('type');
        $(this).select2({
            placeholder: $(this).data('placeholder') || '-- Pilih --',
            allowClear: true,
            dropdownParent: $p,
            ajax: {
                url: $(this).data('ajax-url'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        q: params.term,
                    };

                    if (type === 'student') {
                        query.role = 'student';
                    } else if (type === 'examiner') {
                        query.role = 'examiner';
                    }

                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name
                        }))
                    };
                }
            }
        });
    });

    $('#datatables').DataTable({
        processing: true,
        serverSide: false,
        scrollX: true,
        ajax: '{{ route('admin.thesis.schedule.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'exam_date', name: 'exam_date' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'end_time' },
            { data: 'student.name', name: 'student.name' },
            { data: 'student.thesis.title', name: 'student.thesis.title' },
            { data: 'is_editable', name: 'is_editable' },
            { data: 'action', name: 'action' },
        ],
    });

    $('#datatables').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ route("admin.thesis.schedule.getById", ["id" => ":id"]) }}'.replace(':id', id),
            type: 'GET',
            success: function(data) {
                $('#form_update').attr('action', '{{ route("admin.thesis.schedule.update", ["id" => ":id"]) }}'.replace(':id', id));
                $('#exam_date_update').val(data.exam_date);
                $('#start_time_update').val(data.start_time);
                $('#end_time_update').val(data.end_time);
                $('#room_update').val(data.room);
                $('#is_editable_checkbox_update').prop('checked', !!data.is_editable);

                var studentOption = new Option(data.student.name, data.student.id, true, true);
                $('#student_update').append(studentOption).trigger('change');

                var examiner1Option = new Option(data.primary_examiner.name, data.primary_examiner.id, true, true);
                $('#examiner-1_update').append(examiner1Option).trigger('change');

                var examiner2Option = new Option(data.secondary_examiner.name, data.secondary_examiner.id, true, true);
                $('#examiner-2_update').append(examiner2Option).trigger('change');

                var examiner3Option = new Option(data.tertiary_examiner.name, data.tertiary_examiner.id, true, true);
                $('#examiner-3_update').append(examiner3Option).trigger('change');
            },
            error: function(error) {
                console.error(error);
                Swal.fire(
                    'Error!',
                    'Terjadi kesalahan saat mengambil data kategori.',
                    'error'
                );
            }
        });
    });

    $('#datatables').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="pt-2 mx-5 mt-4 fs-15"><h4>Apakah anda yakin?</h4><p class="mx-4 mb-0 text-muted">Anda tidak akan dapat mengembalikan data ini!</p></div></div>',
            showCancelButton: !0,
            customClass: {
                confirmButton: "btn btn-primary w-xs me-2 mb-1",
                cancelButton: "btn btn-danger w-xs mb-1"
            },
            confirmButtonText: "Ya, Hapus!",
            buttonsStyling: !1,
            showCloseButton: !0
        }).then(function(t) {
            if(t.value) {
                $.ajax({
                    url: '{{ route("admin.thesis.schedule.destroy", ["id" => ":id"]) }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(data) {
                        Swal.fire({
                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon><div class="pt-2 mt-4 fs-15"><h4>Well done !</h4><p class="mx-4 mb-0 text-muted">' +data.message+ '</p></div></div>',
                            showCancelButton: !0,
                            showConfirmButton: !1,
                            customClass: {
                                cancelButton: "btn btn-primary w-xs mb-1"
                            },
                            cancelButtonText: "Back",
                            buttonsStyling: !1,
                            showCloseButton: !0
                        });
                        $('#datatables').DataTable().ajax.reload();
                    },
                    error: function(error) {
                        console.error(error);
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
                        })
                    }
                });
            }
        })
    });
</script>
@endpush