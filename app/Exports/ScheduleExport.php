<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;

class ScheduleExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $title;

    public function __construct($data, $title = 'JADWAL UJIAN TUGAS AKHIR')
    {
        $this->data = $data;

        $examDates = $this->data->pluck('exam_date')->unique()->sort();

        if ($examDates->count() === 1) {
            $this->title = $title . ' - ' . $this->formatDate($examDates->first());
        } elseif ($examDates->count() > 1) {
            $this->title = $title . ' - ' . $this->formatDate($examDates->first()) . ' s/d ' . $this->formatDate($examDates->last());
        } else {
            $this->title = $title;
        }
    }

    public function collection()
    {
        return $this->data->map(function ($schedule, $key) {
            return [
                'No' => $key + 1,
                'NIM' => $schedule->student->identity,
                'Mahasiswa' => $schedule->student->name,
                'Penguji 1' => $schedule->primary_examiner->name ?? '-',
                'Penguji 2' => $schedule->secondary_examiner->name ?? '-',
                'Penguji 3' => $schedule->tertiary_examiner->name ?? '-',
                'Judul Tugas Akhir' => $schedule->student->final_project->title,
                'Waktu Mulai' => $schedule->start_time,
                'Waktu Berakhir' => $schedule->end_time,
                'Ruangan' => $schedule->room,
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'NIM', 'Mahasiswa', 'Penguji 1', 'Penguji 2', 'Penguji 3', 'Judul Tugas Akhir', 'Waktu Mulai', 'Waktu Berakhir', 'Ruangan'];
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
                
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', $this->title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A3:J3')->applyFromArray([
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

                $rowCount = 3 + $this->data->count();

                $sheet->getStyle("A4:J{$rowCount}")->applyFromArray([
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

                $sheet->setAutoFilter('A3:J3');

                foreach (range('A', 'J') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }


    public function startCell(): string
    {
        return 'A3';
    }

    private function formatDate($date): string
    {
        return \Carbon\Carbon::parse($date)->format('d M Y');
    }
}
