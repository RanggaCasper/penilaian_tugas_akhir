<div class="row">
    @if ($exams->isEmpty())
        <p class="text-muted">Belum ada ujian yang tersedia.</p>
    @else
        @foreach ($exams as $list)
            <div class="col-lg-4">
                <x-card title="{{ $list->student->name }} - {{ $list->student->identity }}">
                    <div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-semibold w-50">Judul Tugas Akhir</span>
                                <span class="line-break w-50 text-end">{{ $list->student->thesis->title }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Link Dokumen</span>
                                <a href="{{ $list->student->thesis->document }}" class="btn btn-sm btn-primary" target="_blank">Lihat Dokumen</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Link Dokumen Tambahan</span>
                                <a href="{{ $list->student->thesis->support_document }}" class="btn btn-sm btn-primary" target="_blank">Lihat Dokumen</a>
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
                                <a href="{{ $list->assessments->contains('examiner_id', auth()->id()) ? route('lecturer.thesis.exam.generatePDF', $list->id) : '#' }}" 
                                    class="mb-3 w-100 btn btn-success text-decoration-none"
                                    @if (!$list->assessments->contains('examiner_id', auth()->id()))
                                        onclick="return false;" 
                                        style="pointer-events: none; opacity: 0.6; cursor: not-allowed;"
                                    @endif>
                                    Download Hasil
                                 </a>                                                                                                                      
                                <button type="button" class="w-100 btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal" data-id="{{ $list->id }}" @if (!$list->is_editable)
                                    disabled
                                @endif>
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