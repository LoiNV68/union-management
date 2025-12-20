<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function collection()
    {
        return Transaction::withCount([
            'memberTransactions as paid_count' => function ($query) {
                $query->where('payment_status', '>=', 1);
            }
        ])->orderByDesc('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Tiêu Đề',
            'Mô Tả',
            'Loại',
            'Số Tiền',
            'Đã Thu/Chi',
            'Ngày Hạn',
            'Ngày Tạo',
        ];
    }

    public function map($transaction): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $transaction->title,
            $transaction->description,
            $transaction->type == 0 ? 'Thu' : 'Chi',
            number_format($transaction->amount, 0, ',', '.'),
            $transaction->paid_count,
            $transaction->due_date ? $transaction->due_date->format('d/m/Y') : '',
            $transaction->created_at ? $transaction->created_at->format('d/m/Y') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
