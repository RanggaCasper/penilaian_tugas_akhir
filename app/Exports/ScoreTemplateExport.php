<?php

namespace App\Exports;

use App\Models\Proposal\Proposal;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ScoreTemplateExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithEvents
{
    public function collection()
    {
        $data = Proposal::with('student', 'student.generation', 'score')
            ->where(function ($query) {
                $query->where('primary_mentor_id', Auth::id())
                    ->orWhere('secondary_mentor_id', Auth::id());
            })
            ->where('status', 'disetujui')
            ->get();

        return $data->map(function ($proposal) {
            return [
                'Nama' => $proposal->student->name ?? 'N/A',
                'NIM' => $proposal->student->identity ?? 'N/A',
                'Score' => null,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIM',
            'Score',
        ];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function title(): string
    {
        return 'Nilai Proposal';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->setCellValue('A1', 'Template Excel Nilai Bimbingan');
                $sheet->setCellValue('A2', 'Petunjuk Penggunaan :');
                $sheet->setCellValue('A3', '1. Isi kolom Nama, NIM, dan Score sesuai dengan format.');
                $sheet->setCellValue('A4', '2. Pastikan NIM sudah terdaftar di database.');
                $sheet->setCellValue('A5', '3. Dilarang mengubah format file.');

                $sheet->mergeCells('A1:C1');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:A5')->applyFromArray([
                    'font' => [
                        'italic' => true,
                    ],
                ]);

                foreach (range('A', 'C') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
            },
        ];
    }
}
