@extends('layouts.app')

@section('content')
<h5 class="mb-3">
    Daftar Ujian Tugas Akhir - {{ now()->locale('id')->translatedFormat('d F Y') }}
</h5>
<div class="row">
    @if ($exams->isEmpty())
        <div class="text-center d-flex flex-column justify-content-center align-items-center">
            <div>
                <img src="{{ asset('assets/undraw/no-data.svg') }}" class="img-fluid" width="50%" height="50%" alt="No Data">
            </div>
            <div>
                <p class="mt-3">
                    Belum ada ujian yang tersedia.
                </p>
            </div>
        </div>
    @else
    @foreach ($exams as $list)
        <div class="col-lg-4">
            <x-card title="{{ $list->student->name }}">
                <div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-semibold w-50">Judul Tugas Akhir</span>
                            <span class="line-break w-50 text-end">{{ $list->student->final_project->title }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Link Dokumen</span>
                            <a href="{{ $list->student->final_project->document }}" class="btn btn-sm btn-primary" target="_blank">Lihat Dokumen</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Link Dokumen Tambahan</span>
                            <a href="{{ $list->student->final_project->support_document }}" class="btn btn-sm btn-primary" target="_blank">Lihat Dokumen</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Waktu</span>
                            <span>{{ \Carbon\Carbon::parse($list->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($list->end_time)->format('H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Ruangan</span>
                            <span>{{ $list->room }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Posisi</span>
                            <span>
                                @if ($list->primary_examiner_id == Auth::id())
                                    Penguji 1
                                @elseif ($list->secondary_examiner_id == Auth::id())
                                    Penguji 2
                                @elseif ($list->tertiary_examiner_id == Auth::id())
                                    Penguji 3
                                @else
                                    -
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            @if ($list->evaluations)
                                <a href="{{ route('lecturer.final_project.exam.generatePDF', $list->id) }}" class="mb-3 w-100 btn btn-success">
                                    Download Penilaian
                                </a>
                            @endif
                            <button type="button" class="w-100 btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal" data-id="{{ $list->id }}">
                                Nilai
                            </button>
                        </li>
                    </ul>
                </div>
            </x-card>
        </div>
    @endforeach

    @endif
</div>

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('lecturer.final_project.exam.store') }}" method="POST" id="form">
                    @csrf
                    <select class="form-select" name="exam_evaluation_id" id="evaluation" aria-label="Pilih Form Penilaian">
                        <option value="">-- Pilih Form --</option>
                        @foreach (\App\Models\Evaluation\Evaluation::all() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <div id="form-display" class="mt-3">
                        
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
        $('#modal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const examId = button.data('id');
            const form = $('#form');
            form.find('input[name="exam_id"]').remove();
            form.prepend(`<input type="hidden" name="exam_id" value="${examId}">`);
            $('#evaluation').val('').trigger('change');
            $('#form-display').html('<p class="text-muted">Silakan pilih form terlebih dahulu.</p>');
        });

        $('#evaluation').on('change', function () {
            const evaluationId = $(this).val();
            const examId = $('input[name="exam_id"]').val();

            if (evaluationId) {
                $.ajax({
                    url: '{{ route("lecturer.final_project.exam.getEvaluation", ":id") }}'.replace(':id', evaluationId),
                    method: 'GET',
                    data: { exam_id: examId },
                    success: function (data) {
                        const formContainer = $('#form-display');
                        formContainer.empty();

                        if (Array.isArray(data.evaluation_criterias)) {
                            data.evaluation_criterias.forEach(function (criteria) {
                                if (criteria.has_sub && Array.isArray(criteria.sub_evaluation_criterias) && criteria.sub_evaluation_criterias.length > 0) {
                                    formContainer.append(`
                                        <h5 class="mt-3">${criteria.name}</h5>
                                    `);

                                    const subCriteriaContainer = $('<div></div>');
                                    criteria.sub_evaluation_criterias.forEach(function (subCriteria) {
                                        const subValue = data.scores?.sub_scores?.[subCriteria.id] || '';
                                        subCriteriaContainer.append(`
                                            <div class="mb-2">
                                                <label for="sub_criteria_${subCriteria.id}" class="form-label">${subCriteria.name} (Bobot: ${subCriteria.score}%)</label>
                                                <input type="number" class="form-control" id="sub_criteria_${subCriteria.id}" name="sub_scores[${subCriteria.id}]" min="0" max="100" value="${subValue}">
                                            </div>
                                        `);
                                    });
                                    formContainer.append(subCriteriaContainer);
                                } else {
                                    const value = data.scores?.scores?.[criteria.id] || '';
                                    formContainer.append(`
                                        <div class="mb-3">
                                            <label for="criteria_${criteria.id}" class="form-label">${criteria.name} (Bobot: ${criteria.score}%)</label>
                                            <input type="number" class="form-control" id="criteria_${criteria.id}" name="scores[${criteria.id}]" min="0" max="100" value="${value}">
                                        </div>
                                    `);
                                }
                            });
                        } else {
                            formContainer.html('<p class="text-muted">Tidak ada kriteria untuk form ini.</p>');
                        }
                    },
                    error: function () {
                        $('#form-display').html('<p class="text-muted">Gagal mengambil data.</p>');
                    }
                });
            } else {
                $('#form-display').html('<p class="text-muted">Silakan pilih form terlebih dahulu.</p>');
            }
        });
    });

</script>
@endpush