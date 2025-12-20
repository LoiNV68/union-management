<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return User::orderBy('student_code')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã Sinh Viên',
            'Vai Trò',
            'Trạng Thái Khóa',
            'Ngày Tạo',
        ];
    }

    public function map($user): array
    {
        $this->rowNumber++;

        $role = match ($user->role) {
            0 => 'User',
            1 => 'Admin',
            2 => 'Super Admin',
            default => 'Unknown',
        };

        return [
            $this->rowNumber,
            $user->student_code,
            $role,
            $user->is_locked ? 'Đã khóa' : 'Hoạt động',
            $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
