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

class ScheduleExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $title;

    public function __construct($data, $title = 'JADWAL UJIAN')
    {
        $this->data = $data;
        $examDates = $this->data->pluck('exam_date')->unique()->sort();
        $type = $this->data->pluck('type')->map(function ($type) {
            if ($type === 'final_project') {
                return 'TUGAS AKHIR';
            } elseif ($type === 'proposal') {
                return 'PROPOSAL';
            }
            return $type;
        })->unique()->implode(', ');

        if ($examDates->count() === 1) {
            $this->title = $title . ' ' . $type . ' - ' . $this->formatDate($examDates->first());
        } elseif ($examDates->count() > 1) {
            $this->title = $title . ' ' . $type . ' - ' . $this->formatDate($examDates->first()) . ' s/d ' . $this->formatDate($examDates->last());
        } else {
            $this->title = $title . ' ' . $type;
        }
    }

    public function collection()
    {
        return $this->data->map(function ($schedule, $key) {
            return [
                'No' => $key + 1,
                'NIM' => $schedule->student->identity,
                'Mahasiswa' => $schedule->student->name,
                'Judul' => $schedule->type === 'final_project' 
                            ? ($schedule->student->final_project->title ?? '-') 
                            : ($schedule->type === 'proposal' 
                                ? ($schedule->student->proposal->title ?? '-') 
                                : '-'),
                'Penguji 1' => $schedule->primary_examiner->name ?? '-',
                'Penguji 2' => $schedule->secondary_examiner->name ?? '-',
                'Penguji 3' => $schedule->tertiary_examiner->name ?? '-',
                'Waktu' => $this->formatDateTime($schedule->start_time) . ' - ' . $this->formatDateTime($schedule->end_time),
                'Ruangan' => $schedule->room,
            ];
        });
    }

    private function formatDateTime($dateTime)
    {
        return \Carbon\Carbon::parse($dateTime)->format('H:i');
    }

    public function headings(): array
    {
        return ['No', 'NIM', 'Mahasiswa', 'Judul', 'Penguji 1', 'Penguji 2', 'Penguji 3', 'Waktu', 'Ruangan'];
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
                    'font' => [
                        'bold' => true,
                    ],
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

                $rowCount = 3 + $this->data->count();

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

                $sheet->setAutoFilter('A3:I3');

                foreach (range('A', 'I') as $column) {
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
        return Carbon::parse($date)->locale('id')->isoFormat('DD MMMM YYYY');
    }
}
