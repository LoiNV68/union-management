<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return Member::with(['user', 'branch'])->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã Sinh Viên',
            'Họ và Tên',
            'Ngày Sinh',
            'Giới Tính',
            'Email',
            'Số Điện Thoại',
            'Địa Chỉ',
            'Chi Đoàn',
            'Ngày Vào Đoàn',
            'Trạng Thái',
        ];
    }

    public function map($member): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $member->user?->student_code ?? 'Chưa cập nhật',
            $member->full_name,
            $member->birth_date ? $member->birth_date->format('d/m/Y') : '',
            $member->gender === 0 ? 'Nam' : 'Nữ',
            $member->email,
            $member->phone_number,
            $member->address,
            $member->branch?->branch_name ?? '',
            $member->join_date ? $member->join_date->format('d/m/Y') : '',
            $member->status === 1 ? 'Hoạt động' : 'Không hoạt động',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
