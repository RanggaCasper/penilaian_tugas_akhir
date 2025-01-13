@extends('layouts.app')

@section('title', 'Kelola Pendaftaran Proposal')

@section('content')
    <x-card title="Data Pendaftaran Proposal">   
        <table id="datatables" class="table align-middle nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Pembimbing 1</th>
                    <th>Pembimbing 2</th>
                    <th>Judul</th>
                    <th>Angkatan</th>
                    <th>Status</th>
                    <th>Perubahan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </x-card>

    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="viewData" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewData">Detail Proposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Mahasiswa</label>
                        <div class="col-sm-8" id="detail-student"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">NIM</label>
                        <div class="col-sm-8" id="detail-identity"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Pembimbing 1</label>
                        <div class="col-sm-8" id="detail-primary-mentor"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Pembimbing 2</label>
                        <div class="col-sm-8" id="detail-secondary-mentor"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Judul</label>
                        <div class="col-sm-8" id="detail-title"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Rubrik</label>
                        <div class="col-sm-8" id="rubric"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Rubrik Bimbingan</label>
                        <div class="col-sm-8" id="guidance-rubric"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Dokumen</label>
                        <div class="col-sm-8"><a class="btn btn-sm btn-primary" target="_blank" id="detail-document">Lihat Dokumen</a></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Dokumen Pendukung</label>
                        <div class="col-sm-8" ><a class="btn btn-sm btn-primary" target="_blank" id="detail-support-document">Lihat Dokumen</a></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Angkatan</label>
                        <div class="col-sm-8" id="detail-generation"></div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 fw-bold">Status</label>
                        <div class="col-sm-8 text-capitalize" id="detail-status"></div>
                    </div>

                    <h5 class="fw-bold">Edit Data</h5>
                    <form id="form_update" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="primary_mentor" class="form-label">Pembimbing 1</label>
                            <select id="primary_mentor" name="primary_mentor_id" class="form-control select2" 
                                    data-placeholder="-- Pilih Pembimbing --" 
                                    data-ajax-url="{{ route('admin.proposal.register.getMentor') }}" 
                                    data-type="mentor"></select>
                        </div>
                        <div class="mb-3">
                            <label for="secondary_mentor" class="form-label">Pembimbing 2</label>
                            <select id="secondary_mentor" name="secondary_mentor_id" class="form-control select2" 
                                    data-placeholder="-- Pilih Pembimbing --" 
                                    data-ajax-url="{{ route('admin.proposal.register.getMentor') }}" 
                                    data-type="mentor"></select>
                        </div>
                        <div class="mb-3">
                            <div class="mb-3">
                                <label for="rubric_id" class="form-label">Rubrik</label>
                                <select name="rubric_id" class="form-select" id="rubric_id">
                                    <option selected disabled>-- Pilih Rubrik --</option>
                                    @foreach (\App\Models\Rubric\Rubric::where('program_study_id', auth()->user()->program_study_id)->where('type', 'proposal')->get() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="mb-3">
                                <label for="guidance_rubric_id" class="form-label">Rubrik Bimbingan</label>
                                <select name="guidance_rubric_id" class="form-select" id="guidance_rubric_id">
                                    <option selected disabled>-- Pilih Rubrik --</option>
                                    @foreach (\App\Models\Rubric\Rubric::where('program_study_id', auth()->user()->program_study_id)->where('type', 'guidance')->get() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status_update">Edit Status</label>
                            <select class="form-control form-select" name="status" id="status_update">
                                <option value="menunggu">Menunggu</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="is_editable_update">Izin Perubahan Data</label>
                            <select class="form-control form-select" name="is_editable" id="is_editable_update">
                                <option value="0">Tolak</option>
                                <option value="1">Setujui</option>
                            </select>
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
                    } else if (type === 'mentor') {
                        query.role = 'mentor';
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
        ajax: '{{ route('admin.proposal.register.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'student.name', name: 'student.name' },
            { data: 'student.identity', name: 'student.identity' },
            { data: 'primary_mentor.name', name: 'primary_mentor.name' },
            { data: 'secondary_mentor', name: 'secondary_mentor' },
            { data: 'title', name: 'title' },
            { data: 'student.generation.name', name: 'student.generation.name' },
            { data: 'status', name: 'status' },
            { data: 'is_editable', name: 'is_editable' },
            { data: 'action', name: 'action' },
        ],
    });

    $('#datatables').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ route("admin.proposal.register.getById", ["id" => ":id"]) }}'.replace(':id', id),
            type: 'GET',
            success: function(data) {
                $('#form_update').attr('action', '{{ route("admin.proposal.register.update", ["id" => ":id"]) }}'.replace(':id', id));
                $('#detail-student').text(data.student.name);
                $('#detail-identity').text(data.student.identity);
                $('#detail-primary-mentor').text(data.primary_mentor.name);
                $('#detail-secondary-mentor').text(data.secondary_mentor && data.secondary_mentor.name ? data.secondary_mentor.name : '-');
                $('#detail-title').text(data.title);
                $('#detail-document').attr('href', data.document);
                $('#detail-support-document').attr('href', data.support_document);
                $('#detail-generation').text(data.student.generation.name);
                $('#detail-status').text(data.status);
                $('#rubric').text(data.rubric.name);
                $('#guidance-rubric').text(data.guidance_rubric && data.guidance_rubric.name ? data.guidance_rubric.name : '-');
                $('#rubric_id').val(data.rubric_id);
                $('#guidance_rubric_id').val(data.guidance_rubric_id);
                $('#status_update').val(data.status);
                $('#is_editable_update').val(data.is_editable);
                var primary_mentor = new Option(data.primary_mentor.name, data.primary_mentor.id, true, true);
                $('#primary_mentor').append(primary_mentor).trigger('change');

                var secondary_mentor = new Option(data.secondary_mentor.name, data.secondary_mentor.id, true, true);
                $('#secondary_mentor').append(secondary_mentor).trigger('change');
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
                    url: '{{ route("admin.proposal.register.destroy", ["id" => ":id"]) }}'.replace(':id', id),
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