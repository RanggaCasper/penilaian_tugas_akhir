@extends('layouts.app')

@section('content')
<x-card title="Data Mahasiswa Bimbingan">   
    <table id="datatables" class="table align-middle nowrap">
        <thead>
            <tr>
                <th>No</th>
                <th>Mahasiswa</th>
                <th>NIM</th>
                <th>Judul</th>
                <th>Posisi</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</x-card>

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModal">Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form">
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
    $('#datatables').DataTable({
        processing: true,
        serverSide: false,
        scrollX: true,
        ajax: '{{ route('lecturer.mentor.get') }}',
        columns: [
            { data: 'no', name: 'no' },
            { data: 'student.name', name: 'student.name' },
            { data: 'student.identity', name: 'student.identity' },
            { data: 'title', name: 'title' },
            { data: 'position', name: 'position' }, 
            { data: 'action', name: 'action' },
        ],
    });
    
    $('#modal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const form = $('#form');
        const formContainer = $('#form-display');
        form.find('input[name="proposal_id"]').remove();
        form.prepend(`<input type="hidden" name="proposal_id" value="${id}">`);
        
        $.ajax({
            url: '{{ route("lecturer.mentor.getRubric", ":id") }}'.replace(':id', id),
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
                            
                            if (Array.isArray(criteria.sub_criterias) && criteria.sub_criterias.length > 0) {
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
                                subCriteriaContainer.append(`<p class="text-muted">Tidak ada sub kriteria untuk ${criteria.name}.</p>`);
                                formContainer.append(subCriteriaContainer);
                            }
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
                } else {
                    formContainer.html('<p class="text-muted">Tidak ada rubrik untuk form ini.</p>');
                }
            },
            error: function (error) {
                formContainer.html('<p class="text-muted">'+ error.responseJSON.message +'</p>');
            }
        });
    });
</script>
@endpush