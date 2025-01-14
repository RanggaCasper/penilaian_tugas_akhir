<?php

namespace App\Services;

use App\Models\Exam\Exam;
use App\Models\Rubric\Rubric;
use App\Models\Exam\Assessment;

class AssessmentService
{
    /**
     * Hitung total skor untuk mahasiswa tertentu.
     *
     * @param int $studentId
     * @return array
     */
    public function calculateScore(int $studentId, ?string $type = null): array
    {
        $query = Exam::with([
            'assessments.scores.criteria',
            'assessments.scores.sub_scores.sub_criteria',
            'primary_examiner',
            'secondary_examiner',
            'tertiary_examiner',
        ])->whereHas('assessments', function ($query) use ($studentId, $type) {
            $query->where('student_id', $studentId);

            if ($type) {
                $query->where('type', $type);
            }
        });

        $exams = $query->get();

        if ($exams->isEmpty()) {
            return [];
        }

        $result = $exams->flatMap(function ($exam) {
            return $exam->assessments->map(function ($assessment) {
                $totalScore = collect($assessment->scores)->sum(function ($score) {
                    $mainScore = ($score->score ?? 0) * (($score->criteria->weight ?? 0) / 100);
                    $subTotal = collect($score->sub_scores)->sum(function ($subScore) {
                        return ($subScore->score ?? 0) * (($subScore->sub_criteria->weight ?? 0) / 100);
                    });
                    return $mainScore + $subTotal;
                });

                $examinerPosition = '-';
                if ($assessment->exam->primary_examiner_id === $assessment->examiner->id) {
                    $examinerPosition = 'Penguji 1';
                } elseif ($assessment->exam->secondary_examiner_id === $assessment->examiner->id) {
                    $examinerPosition = 'Penguji 2';
                } elseif ($assessment->exam->tertiary_examiner_id === $assessment->examiner->id) {
                    $examinerPosition = 'Penguji 3';
                }

                return [
                    'id' => $assessment->examiner->id,
                    'position' => $examinerPosition,
                    'name' => $assessment->examiner->name ?? '-',
                    'score' => $totalScore,
                ];
            });
        });

        $orderedResult = $result->sortBy(function ($item) {
            $positionOrder = [
                'Penguji 1' => 1,
                'Penguji 2' => 2,
                'Penguji 3' => 3,
            ];
            return $positionOrder[$item['position']] ?? 999;
        })->values();

        $groupedScores = $orderedResult->groupBy('position')->map(function ($scores) {
            return $scores->avg('score');
        });

        $averageScore = collect([
            $groupedScores->get('Penguji 1', 0),
            $groupedScores->get('Penguji 2', 0),
            $groupedScores->get('Penguji 3', 0),
        ])->avg();

        $weight = 0.0;
        if ($type === 'proposal') {
            $weight = 0.1;
        } elseif ($type === 'thesis') {
            $weight = 0.3;
        }

        $finalScore = $averageScore * $weight;

        return [
            'scores' => $orderedResult->toArray(),
            'average_score' => $averageScore,
            'final_score' => $finalScore,
        ];
    }

    public function calculateScoreGuidance(int $studentId): array
    {
        $rubric = Rubric::with('criterias.sub_criterias')
                    ->where('type', 'guidance')
                    ->first();

        if (!$rubric) {
            return [
                'status' => false,
                'message' => 'Rubrik tidak ditemukan.',
            ];
        }
  
        $assessments = Assessment::with([
            'scores.criteria',
            'scores.sub_scores.sub_criteria',
            'questions',
            'revisions',
        ])
        ->where('student_id', $studentId)
        ->where('rubric_id', $rubric->id)
        ->get();

        if ($assessments->isEmpty()) {
            return [
                'status' => false,
                'message' => 'Tidak ada data penilaian.',
            ];
        }
       
        $result = $assessments->map(function ($assessment, $index) {
            $totalScore = collect($assessment->scores)->sum(function ($score) {
                $mainScore = ($score->score ?? 0) * (($score->criteria->weight ?? 0) / 100);
                $subTotal = collect($score->sub_scores)->sum(function ($subScore) {
                    return ($subScore->score ?? 0) * (($subScore->sub_criteria->weight ?? 0) / 100);
                });
                return $mainScore + $subTotal;
            });

            return [
                'id' => $assessment->examiner->id,
                'position' => 'Pembimbing ' . ($index + 1),
                'name' => $assessment->examiner->name ?? '-',
                'score' => $totalScore,
                'final_score' => $totalScore * 0.3,
            ];
        });

        $averageScore = $result->avg('score');

        return [
            'scores' => $result->toArray(),
            'average_score' => $averageScore,
            'final_score' => $result->sum('final_score')
        ];
    }
}
