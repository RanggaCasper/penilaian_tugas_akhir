@extends('layouts.app')

@section('content')
<div class="mb-3 d-flex justify-content-between">
    <h5>
        Daftar Ujian Tugas Akhir - {{ now()->locale('id')->translatedFormat('d F Y') }}
    </h5>
    <button class="btn btn-primary" id="reload">Muat Ulang</button>
</div>

<div id="exam_display">
    <p class="text-muted">Sedang memuat data ...</p>
</div>

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('lecturer.thesis.exam.store') }}" data-success="fetchData" method="POST" id="form">
                    @csrf
                    <h5 class="mb-3">Nilai</h5>
                    <div id="form-display" class="mt-3">
                        <p class="text-muted">Sedang memuat data ...</p>
                    </div>
                    <h5 class="mb-3">Pertanyaan</h5>
                    <div id="questions-display" class="mb-3"></div>
                    <h5 class="mb-3">Revisi</h5>
                    <div id="revisions-display" class="mb-3"></div>
                    <x-button type="reset" class="btn btn-danger" label="Reset" />
                    <x-button type="submit" class="btn btn-primary" label="Submit" />
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    fetchData();
    
    $('#reload').on('click', function () {
        fetchData();
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

    questionsContainer.html('');
    revisionsContainer.html('');

    let questionIndex = 1;
    let revisionIndex = 1;

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

                questions.forEach(function (question) {
                    questionsContainer.append(`
                        <div class="mb-3 question-item" data-index="${questionIndex}">
                            <label for="question_${questionIndex}" class="form-label">Pertanyaan</label>
                            <input type="text" class="form-control" id="question_${questionIndex}" 
                                name="questions[${questionIndex}][question]" value="${question.question}">
                            <label for="question_weight_${questionIndex}" class="form-label">Bobot</label>
                            <input type="number" class="form-control" id="question_weight_${questionIndex}" 
                                name="questions[${questionIndex}][weight]" min="0" max="100" value="${question.weight}">
                            <button type="button" class="btn btn-sm btn-danger remove-question">Hapus</button>
                        </div>
                    `);
                    questionIndex++;
                });

                questionsContainer.append(`
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-primary add-question">Tambah Pertanyaan</button>
                    </div>
                `);

                revisions.forEach(function (revision) {
                    revisionsContainer.append(`
                        <div class="mb-3 revision-item" data-index="${revisionIndex}">
                            <label for="revision_description_${revisionIndex}" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="revision_description_${revisionIndex}" 
                                name="revisions[${revisionIndex}][description]" value="${revision.description}">
                            <label for="revision_chapter_${revisionIndex}" class="form-label">Bab</label>
                            <input type="text" class="form-control" id="revision_chapter_${revisionIndex}" 
                                name="revisions[${revisionIndex}][chapter]" value="${revision.chapter}">
                            <label for="revision_page_${revisionIndex}" class="form-label">Halaman</label>
                            <input type="text" class="form-control" id="revision_page_${revisionIndex}" 
                                name="revisions[${revisionIndex}][page]" value="${revision.page}">
                            <button type="button" class="btn btn-sm btn-danger remove-revision">Hapus</button>
                        </div>
                    `);
                    revisionIndex++;
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
        const newIndex = questionIndex;
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
        questionIndex++;
    });

    $(document).on('click', '.add-revision', function () {
        const newIndex = revisionIndex;
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
            revisionIndex++;
        });

        $(document).on('click', '.remove-question', function () {
            $(this).closest('.question-item').remove();
        });

        $(document).on('click', '.remove-revision', function () {
            $(this).closest('.revision-item').remove();
        });
    });

});

function fetchData() {
    $.ajax({
        url: `{{ route('lecturer.thesis.exam.get') }}`,
        type: 'GET',
        beforeSend: function () {
            $('#exam_display').html('<p class="text-muted">Sedang memuat data...</p>');
        },
        success: function (response) {
            if (response) {
                $('#exam_display').html(response.html);
            }
        },
        error: function (error) {
            $('#exam_display').html('<p class="text-muted">'+error.responseJSON.message+'</p>');
        }
    });
}
</script>
@endpush