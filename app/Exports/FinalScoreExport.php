<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class FinalScoreExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $title;

    public function __construct($data, $period)
    {
        $this->data = $data;
        $this->title = "LAPORAN PENILAIAN AKHIR - $period";
    }

    public function collection()
    {
        $rows = [];
        foreach ($this->data as $key => $item) {
            $assessment = $item['assessment'] ?? [];
            $guidanceScores = $assessment['guidance_score']['scores'] ?? [];

            $rows[] = [
                'No' => $key + 1,
                'Nama' => $item['student']['name'] ?? '-',
                'NIM' => $item['student']['identity'] ?? '-',
                'Nilai Proposal (Rata-rata)' => $assessment['proposal_score']['average_score'] ?? '-',
                'Nilai Pembimbing 1' => $this->getScoreByPosition($guidanceScores, 'Pembimbing 1'),
                'Nilai Pembimbing 2' => $this->getScoreByPosition($guidanceScores, 'Pembimbing 2'),
                'Nilai Tugas Akhir (Rata-rata)' => $assessment['thesis_score']['average_score'] ?? '-',
                'Nilai Akhir Angka' => $assessment['total_score'] ?? '-',
                'Nilai Akhir Huruf' => $this->convertToGrade($assessment['total_score'] ?? 0),
            ];
        }
        return collect($rows);
    }

    private function getScoreByPosition($scores, $position)
    {
        $score = collect($scores)->firstWhere('position', $position);
        return $score['score'] ?? '-';
    }

    private function convertToGrade($score)
    {
        if ($score >= 81) {
            return 'A (Istimewa)';
        } elseif ($score >= 76) {
            return 'AB (Baik Sekali)';
        } elseif ($score >= 66) {
            return 'B (Baik)';
        } elseif ($score >= 61) {
            return 'BC (Cukup Baik)';
        } elseif ($score >= 56) {
            return 'C (Cukup)';
        } elseif ($score >= 41) {
            return 'D (Kurang)';
        } else {
            return 'E (Kurang Sekali)';
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIM',
            'Nilai Proposal (Rata-rata)',
            'Nilai Pembimbing 1',
            'Nilai Pembimbing 2',
            'Nilai Tugas Akhir (Rata-rata)',
            'Nilai Akhir Angka',
            'Nilai Akhir Huruf',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', $this->title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            
                $sheet->getStyle('A3:I3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '008000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $rowCount = 3 + count($this->data);
                $sheet->getStyle("A4:I{$rowCount}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'F5F5F5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            
                foreach (range('A', 'I') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->setAutoFilter('A3:I3');
            },
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }
}
