<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivitiesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return Activity::withCount('registrations')->orderByDesc('start_date')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Tên Hoạt Động',
            'Địa Điểm',
            'Ngày Bắt Đầu',
            'Ngày Kết Thúc',
            'Loại',
            'Số Người Tối Đa',
            'Số Người Đăng Ký',
        ];
    }

    public function map($activity): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $activity->activity_name,
            $activity->location,
            $activity->start_date ? $activity->start_date->format('d/m/Y') : '',
            $activity->end_date ? $activity->end_date->format('d/m/Y') : '',
            $activity->type,
            $activity->max_participants,
            $activity->registrations_count,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
