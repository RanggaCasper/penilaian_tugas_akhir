@extends('layouts.app')

@section('content')
<div class="mb-3 d-flex justify-content-between">
    <h5>
        Daftar Ujian Proposal - {{ now()->locale('id')->translatedFormat('d F Y') }}
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
                <form action="{{ route('lecturer.proposal.exam.store') }}" data-success="fetchData" method="POST" id="form">
                    @csrf
                    <div id="form-display" class="mt-3">
                        <p class="text-muted">Sedang memuat data ...</p>
                    </div>
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
        form.find('input[name="exam_id"]').remove();
        form.prepend(`<input type="hidden" name="exam_id" value="${examId}">`);
        
        $.ajax({
            url: '{{ route("lecturer.proposal.exam.getRubric", ":id") }}'.replace(':id', examId),
            method: 'GET',
            beforeSend: function () {
                $('#form-display').html('<p class="text-muted">Sedang memuat data...</p>');
            },
            success: function (data) {
                formContainer.empty();
                
                if (data.rubric && Array.isArray(data.rubric.criterias)) {
                    const assessment = data.assessment || {};
                    const assessmentScores = assessment.scores || [];
                    const assessmentSubScores = assessment.scores ? assessment.scores.flatMap(s => s.sub_scores || []) : [];

                    
                    data.rubric.criterias.forEach(function (criteria) {
                        if (criteria.has_sub && Array.isArray(criteria.sub_criterias) && criteria.sub_criterias.length > 0) {
                            formContainer.append(`<h5 class="mt-3">${criteria.name}</h5>`);
                            
                            const subCriteriaContainer = $('<div></div>');
                            
                            
                            criteria.sub_criterias.forEach(function (subCriteria) {
                                const subScore = assessmentSubScores.find(sub => sub.sub_criteria_id === subCriteria.id);
                                const subValue = subScore ? subScore.score : '';  

                                subCriteriaContainer.append(`
                                    <div class="mb-2">
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

                    formContainer.append(`
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Feedback</label>
                            <textarea class="form-control" id="feedback" name="feedback" placeholder="Berikan saran atau kritik." rows="4">${data.assessment.feedback || ''}</textarea>
                        </div>
                    `);
                } else {
                    formContainer.html('<p class="text-muted">Tidak ada rubrik untuk form ini.</p>');
                }
            },
            error: function () {
                formContainer.html('<p class="text-muted">Gagal mengambil data.</p>');
            }
        });
    });
});

function fetchData() {
    $.ajax({
        url: `{{ route('lecturer.proposal.exam.get') }}`,
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