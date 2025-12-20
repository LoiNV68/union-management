<?php

namespace App\Exports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BranchesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return Branch::with('secretaryMember')->withCount('members')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Tên Chi Đoàn',
            'Bí Thư',
            'Mô tả',
            'Số Lượng Thành Viên',
        ];
    }

    public function map($branch): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $branch->branch_name,
            $branch->secretaryMember ? $branch->secretaryMember->student_code . ' - ' . $branch->secretaryMember->full_name : 'Chưa có',
            $branch->description,
            $branch->members_count,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
