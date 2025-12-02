<?php

namespace App\Livewire\Union;

use App\Models\MemberTransaction;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MemberTransactions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public int $perPage = 10;
    public ?int $viewingId = null;
    public bool $showQrModal = false;
    public string $qrCode = '';

    public function mount(): void
    {
        abort_unless(Auth::user()?->role === 0, 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = (int) $value;
        $this->resetPage();
    }

    public function openQrModal(int $id): void
    {
        $this->viewingId = $id;
        $this->showQrModal = true;
        $this->generateQrCode($id);
    }

    public function closeQrModal(): void
    {
        $this->showQrModal = false;
        $this->viewingId = null;
        $this->qrCode = '';
    }

    private function generateQrCode(int $memberTransactionId): void
    {
        $memberTransaction = MemberTransaction::with(['transaction', 'member.user'])
            ->findOrFail($memberTransactionId);

        $amount = $memberTransaction->transaction->amount;
        $description = ($memberTransaction->member->user?->student_code ?: 'THANHVIEN')
            . ' - ' . $memberTransaction->transaction->title;

        // Dùng vietqr.io API miễn phí (QR động, quét được mọi ngân hàng)
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://api.vietqr.io/v2/generate', [
            'bank' => '970422',        // MB Bank (có thể đổi: 970422=Techcombank, 970436=Vietcombank)
            'accountNo' => '906082004',    // Tài khoản test công khai, bị chặn chuyển tiền thật
            'accountName' => 'NGUYEN VAN LOI',
            'amount' => $amount,
            'description' => $description,
            'template' => 'compact2'
        ]);

        $qrDataURL = $response->json('data.qrDataURL');

        if ($response->successful() && $qrDataURL !== null) {
            $this->qrCode = $qrDataURL;
        } else {
            // Fallback: tạo QR tĩnh nếu API lỗi
            $this->qrCode = "https://img.vietqr.io/image/970422-906082004-NGUYENVANLOI-compact2.png?"
                . http_build_query([
                    'amount' => $amount,
                    'addInfo' => $description
                ]);
        }

        // Lưu tạm description để dùng khi fake pay
        $this->dispatch('qr-generated', description: $description);
    }

    public function fakePaymentSuccess(): void
    {
        if (!$this->viewingId)
            return;

        $memberTransaction = MemberTransaction::findOrFail($this->viewingId);

        // Kiểm tra quyền (chỉ thành viên đó được bấm)
        $member = Member::where('user_id', Auth::id())->first();
        if (!$member || $member->id !== $memberTransaction->member_id) {
            $this->addError('payment', 'Không có quyền!');
            return;
        }

        // Tự động cập nhật thành "Đã thanh toán" (chờ xác nhận)
        $memberTransaction->update([
            'payment_status' => 1,
            'payment_date' => now(),
        ]);

        $this->dispatch('payment-marked');
        $this->dispatch('toast', message: 'Thanh toán thành công (demo)!', type: 'success');
    }

    public function markAsPaid(): void
    {
        if (!$this->viewingId) {
            return;
        }

        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            $this->addError('payment', 'Bạn không phải là thành viên.');
            return;
        }

        $memberTransaction = MemberTransaction::findOrFail($this->viewingId);

        if ($memberTransaction->member_id !== $member->id) {
            $this->addError('payment', 'Không có quyền cập nhật.');
            return;
        }

        $memberTransaction->update([
            'payment_status' => 1, // Đã thanh toán (chờ xác nhận)
            'payment_date' => now(),
        ]);

        $this->dispatch('payment-marked');
        $this->closeQrModal();
    }

    public function render()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        return view('livewire.union.member-transactions', [
            'memberTransactions' => $member ? MemberTransaction::query()
                ->where('member_id', $member->id)
                ->with(['transaction'])
                ->whereHas('transaction', function ($query) {
                    $query->where('status', 0) // Only active transactions
                        ->when($this->search, function ($q) {
                            $q->where('title', 'like', '%' . $this->search . '%');
                        });
                })
                ->orderByDesc('created_at')
                ->paginate($this->perPage)
                ->withQueryString() : collect(),
            'viewingTransaction' => $this->viewingId ? MemberTransaction::with(['transaction'])->find($this->viewingId) : null,
        ]);
    }
}
