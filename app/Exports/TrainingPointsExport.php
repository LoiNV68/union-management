<?php

namespace App\Exports;

use App\Models\TrainingPoint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TrainingPointsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return TrainingPoint::with(['member.user', 'member.branch', 'semester'])->orderByDesc('updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã Sinh Viên',
            'Họ Tên',
            'Chi Đoàn',
            'Năm Học',
            'Học Kỳ',
            'Điểm Rèn Luyện',
            'Ghi Chú',
        ];
    }

    public function map($tp): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $tp->member?->user?->student_code ?? '',
            $tp->member?->full_name ?? '',
            $tp->member?->branch?->branch_name ?? '',
            $tp->semester?->school_year ?? '',
            $tp->semester?->semester ?? '',
            $tp->point,
            $tp->note,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
