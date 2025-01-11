<?php

namespace App\Imports;

use App\Models\Mentor\Score;
use App\Models\Proposal\Proposal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class ScoresImport implements ToCollection
{
    public $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index < 7) continue;

            $nim = $row[1];
            if (!$nim) {
                $this->errors[] = "Baris " . ($index + 1) . ": NIM kosong.";
                continue;
            }

            $proposal = Proposal::whereHas('student', function ($query) use ($nim) {
                $query->where('identity', $nim);
            })
            ->where(function ($query) {
                $query->where('primary_mentor_id', Auth::id())
                      ->orWhere('secondary_mentor_id', Auth::id());
            })
            ->where('status', 'disetujui')
            ->first();

            if (!$proposal) {
                $this->errors[] = "Baris " . ($index + 1) . ": Proposal tidak ditemukan untuk NIM $nim.";
                continue;
            }

            $score = $row[2];
            if (!is_numeric($score) || $score < 0 || $score > 100) {
                $this->errors[] = "Baris " . ($index + 1) . ": Nilai harus berupa angka antara 0 dan 100.";
                continue;
            }

            Score::updateOrCreate(
                [
                    'proposal_id' => $proposal->id,
                    'mentor_id' => Auth::id(),
                ],
                [
                    'score' => $score,
                ]
            );
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
