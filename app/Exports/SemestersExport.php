<?php

namespace App\Exports;

use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SemestersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return Semester::orderByDesc('school_year')->orderByDesc('semester')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Năm Học',
            'Học Kỳ',
            'Ngày Bắt Đầu',
            'Ngày Kết Thúc',
        ];
    }

    public function map($semester): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $semester->school_year,
            $semester->semester,
            $semester->start_date ? $semester->start_date->format('d/m/Y') : '',
            $semester->end_date ? $semester->end_date->format('d/m/Y') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
