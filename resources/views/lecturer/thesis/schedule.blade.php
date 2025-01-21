@extends('layouts.app')

@section('title', 'Jadwal Ujian Tugas Akhir')

@section('content')
<x-card title="Data Jadwal Ujian Tugas Akhir">   
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

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModal">Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('lecturer.thesis.exam.store') }}" method="POST" id="form">
                    @csrf
                    <div id="form-display" class="mt-3">
                        <p class="text-muted">Sedang memuat data ...</p>
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
                url: '{{ route("lecturer.thesis.schedule.get") }}',
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
        ajax: '{{ route('lecturer.thesis.schedule.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'exam_date', name: 'exam_date' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'end_time' },
            { data: 'student.name', name: 'student.name' },
            { data: 'position', name: 'position' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action' },
        ],
    });

    $('#modal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const examId = button.data('id');
        const form = $('#form');
        const formContainer = $('#form-display');
        const questionsContainer = $('#questions-display');
        const revisionsContainer = $('#revisions-display');

        form.find('input[name="exam_id"]').remove();
        form.prepend(`<input type="hidden" name="exam_id" value="${examId}">`);

        // Reset content when modal is opened
        questionsContainer.html('');
        revisionsContainer.html('');
        
    $.ajax({
        url: '{{ route("lecturer.thesis.exam.getRubric", ":id") }}'.replace(':id', examId),
        method: 'GET',
        beforeSend: function () {
            $('#form-display').html('<p class="text-muted">Sedang memuat data...</p>');
            $('#questions-display').html('');
            $('#revisions-display').html('');
        },
        success: function (data) {
            formContainer.empty();

            if (data.rubric && Array.isArray(data.rubric.criterias)) {
                const assessment = data.assessment || {};
                const assessmentScores = assessment.scores || [];
                const assessmentSubScores = assessment.scores
                    ? assessment.scores.flatMap(s => s.sub_scores || [])
                    : [];
                const questions = assessment.questions || [];
                const revisions = assessment.revisions || [];

                data.rubric.criterias.forEach(function (criteria) {
                    if (criteria.has_sub && Array.isArray(criteria.sub_criterias) && criteria.sub_criterias.length > 0) {
                        formContainer.append(`<h5 class="mt-3">${criteria.name}</h5>`);

                        const subCriteriaContainer = $('<div class="mb-3"></div>');
                        criteria.sub_criterias.forEach(function (subCriteria) {
                            const subScore = assessmentSubScores.find(
                                sub => sub.sub_criteria_id === subCriteria.id
                            );
                            const subValue = subScore ? subScore.score : '';

                            subCriteriaContainer.append(`
                                <div class="mb-3">
                                    <label for="sub_criteria_${subCriteria.id}" class="form-label">
                                        ${subCriteria.name} (Bobot: ${subCriteria.weight}%)
                                    </label>
                                    <input type="number" class="form-control" id="sub_criteria_${subCriteria.id}" 
                                        name="sub_scores[${criteria.id}][${subCriteria.id}]" min="0" max="100" value="${subValue}">
                                </div>
                            `);
                        });

                        formContainer.append(subCriteriaContainer);
                    } else {
                        const score = assessmentScores.find(s => s.criteria_id === criteria.id)?.score || '';

                        formContainer.append(`
                            <div class="mb-3">
                                <label for="criteria_${criteria.id}" class="form-label">
                                    ${criteria.name} (Bobot: ${criteria.weight}%)
                                </label>
                                <input type="number" class="form-control" id="criteria_${criteria.id}" 
                                    name="scores[${criteria.id}]" min="0" max="100" value="${score}">
                            </div>
                        `);
                    }
                });

                // Add existing questions
                questions.forEach(function (question, index) {
                    questionsContainer.append(`
                        <div class="mb-3 question-item" data-index="${index + 1}">
                            <label for="question_${index + 1}" class="form-label">Pertanyaan</label>
                            <input type="text" class="form-control" id="question_${index + 1}" 
                                name="questions[${index + 1}][question]" value="${question.question}">
                            <label for="question_weight_${index + 1}" class="form-label">Bobot</label>
                            <input type="number" class="form-control" id="question_weight_${index + 1}" 
                                name="questions[${index + 1}][weight]" min="0" max="100" value="${question.weight}">
                            <button type="button" class="btn btn-sm btn-danger remove-question">Hapus</button>
                        </div>
                    `);
                });

                questionsContainer.append(`
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-primary add-question">Tambah Pertanyaan</button>
                    </div>
                `);

                // Add existing revisions
                revisions.forEach(function (revision, index) {
                    revisionsContainer.append(`
                        <div class="mb-3 revision-item" data-index="${index + 1}">
                            <label for="revision_description_${index + 1}" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="revision_description_${index + 1}" 
                                name="revisions[${index + 1}][description]" value="${revision.description}">
                            <label for="revision_chapter_${index + 1}" class="form-label">Bab</label>
                            <input type="text" class="form-control" id="revision_chapter_${index + 1}" 
                                name="revisions[${index + 1}][chapter]" value="${revision.chapter}">
                            <label for="revision_page_${index + 1}" class="form-label">Halaman</label>
                            <input type="text" class="form-control" id="revision_page_${index + 1}" 
                                name="revisions[${index + 1}][page]" value="${revision.page}">
                            <button type="button" class="btn btn-sm btn-danger remove-revision">Hapus</button>
                        </div>
                    `);
                });

                revisionsContainer.append(`
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-primary add-revision">Tambah Revisi</button>
                    </div>
                `);
            } else {
                formContainer.html('<p class="text-muted">Tidak ada rubrik untuk form ini.</p>');
            }
        },
        error: function (error) {
            formContainer.html('<p class="text-muted">' + error.responseJSON.message + '</p>');
        }
    });

    $(document).on('click', '.add-question', function () {
        const newIndex = $('.question-item').length + 1;  // Start index from 1
        questionsContainer.append(`
            <div class="mb-3 question-item" data-index="${newIndex}">
                <label for="question_${newIndex}" class="form-label">Pertanyaan</label>
                <input type="text" class="form-control" id="question_${newIndex}" 
                    name="questions[${newIndex}][question]" value="">
                <label for="question_weight_${newIndex}" class="form-label">Bobot</label>
                <input type="number" class="form-control" id="question_weight_${newIndex}" 
                    name="questions[${newIndex}][weight]" min="0" max="100" value="">
                <button type="button" class="btn btn-sm btn-danger remove-question">Hapus</button>
            </div>
        `);
    });

    $(document).on('click', '.add-revision', function () {
        const newIndex = $('.revision-item').length + 1;  // Start index from 1
        revisionsContainer.append(`
            <div class="mb-3 revision-item" data-index="${newIndex}">
                <label for="revision_description_${newIndex}" class="form-label">Deskripsi</label>
                <input type="text" class="form-control" id="revision_description_${newIndex}" 
                    name="revisions[${newIndex}][description]" value="">
                <label for="revision_chapter_${newIndex}" class="form-label">Bab</label>
                <input type="text" class="form-control" id="revision_chapter_${newIndex}" 
                    name="revisions[${newIndex}][chapter]" value="">
                <label for="revision_page_${newIndex}" class="form-label">Halaman</label>
                <input type="text" class="form-control" id="revision_page_${newIndex}" 
                    name="revisions[${newIndex}][page]" value="">
                <button type="button" class="btn btn-sm btn-danger remove-revision">Hapus</button>
            </div>
        `);
    });

    $(document).on('click', '.remove-question', function () {
        $(this).closest('.question-item').remove();
        });

        $(document).on('click', '.remove-revision', function () {
            $(this).closest('.revision-item').remove();
        });
    });
</script>
@endpush