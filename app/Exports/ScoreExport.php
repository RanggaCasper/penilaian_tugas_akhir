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

class ScoreExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $title;

    public function __construct($data, $period, $type)
    {

        $this->data = $data;
        $this->title = "LAPORAN PENILAIAN $type - $period";
    }    

    public function collection()
    {
        $rows = [];
        foreach ($this->data as $key => $item) {
            $assessment = $item['assessment'] ?? [];
            $rows[] = [
                'No' => $key + 1,
                'Nama' => $item['student']['name'] ?? '-',
                'NIM' => $item['student']['identity'] ?? '-',
                'Penguji 1' => $this->getScoreByPosition($assessment['scores'] ?? [], 'Penguji 1'),
                'Penguji 2' => $this->getScoreByPosition($assessment['scores'] ?? [], 'Penguji 2'),
                'Penguji 3' => $this->getScoreByPosition($assessment['scores'] ?? [], 'Penguji 3'),
                'Rata-rata' => $assessment['average_score'] ?? '-',
            ];
        }
        return collect($rows);
    }

    private function getScoreByPosition($scores, $position)
    {
        $score = collect($scores)->firstWhere('position', $position);
        return $score['score'] ?? '-';
    }

    public function headings(): array
    {
        return ['No', 'Nama', 'NIM', 'Penguji 1', 'Penguji 2', 'Penguji 3', 'Rata-rata'];
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

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', $this->title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A3:G3')->applyFromArray([
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
                $sheet->getStyle("A4:G{$rowCount}")->applyFromArray([
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

                foreach (range('A', 'G') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->setAutoFilter('A3:G3');
            },
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }
}
