<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use App\Models\MemberTransaction;
use App\Models\Member;
use App\Models\Branch;
use App\Models\Notification;
use App\Events\TransactionUpdated;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

class ManageTransactions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $memberSearch = '';
    public string $classSearch = '';
    public int $perPage = 10;
    public bool $showCreateForm = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $viewingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $amount = '';
    public int $type = 0;
    public string $due_date = '';

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()?->role, [1, 2]), 403);
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

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showCreateForm = true;
        $this->editingId = null;
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function openEditForm(int $id): void
    {
        $transaction = Transaction::findOrFail($id);

        // Chỉ cho phép chỉnh sửa transactions do mình tạo
        abort_unless($transaction->created_by === Auth::id(), 403);

        $this->editingId = $id;
        $this->title = $transaction->title;
        $this->description = $transaction->description ?? '';
        $this->amount = $transaction->amount;
        $this->type = $transaction->type;
        $this->due_date = $transaction->due_date?->format('Y-m-d') ?? '';
        $this->showCreateForm = true;
    }

    public function openViewModal(int $id): void
    {
        $transaction = Transaction::findOrFail($id);

        // Chỉ cho phép xem transactions do mình tạo
        abort_unless($transaction->created_by === Auth::id(), 403);

        $this->viewingId = $id;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingId = null;
        $this->memberSearch = '';
        $this->classSearch = '';
    }

    public function openDeleteModal(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function saveTransaction(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|gt:0',
            'type' => 'required|in:0,1',
            'due_date' => 'nullable|date',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'amount' => $this->amount,
            'type' => $this->type,
            'due_date' => $this->due_date ?: null,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            $transaction = Transaction::findOrFail($this->editingId);

            // Chỉ cho phép cập nhật transactions do mình tạo
            abort_unless($transaction->created_by === Auth::id(), 403);

            $transaction->update($data);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Đã cập nhật giao dịch thành công.'
            ]);
            TransactionUpdated::dispatch(Transaction::find($this->editingId));
        } else {
            $transaction = Transaction::create($data);

            // Create member_transactions for all active members if it's a revenue transaction
            if ($transaction->type === 0) {
                $members = Member::where('status', 1)->get();
                $totalMembersCount = $members->count();

                foreach ($members as $member) {
                    MemberTransaction::create([
                        'transaction_id' => $transaction->id,
                        'member_id' => $member->id,
                        'payment_status' => 0,
                    ]);
                }

                // Calculate amount per member: tổng số tiền / tổng số thành viên
                $amountPerMember = $totalMembersCount > 0 ? $transaction->amount / $totalMembersCount : 0;

                // Create revenue transactions for branch officers (cán bộ đoàn)
                $branches = Branch::whereNotNull('secretary')->with('members')->get();
                foreach ($branches as $branch) {
                    $branchActiveMembersCount = $branch->members()->where('status', 1)->count();

                    if ($branchActiveMembersCount > 0 && $branch->secretary) {
                        // Số tiền thu của chi đoàn = số tiền mỗi thành viên × số thành viên trong chi đoàn
                        $branchRevenueAmount = $amountPerMember * $branchActiveMembersCount;

                        Transaction::create([
                            'title' => 'Thu từ: ' . $transaction->title . ' - ' . $branch->branch_name,
                            'description' => 'Khoản thu từ khoản thu tổng: ' . $transaction->title . '. Chi đoàn: ' . $branch->branch_name . '. Số thành viên trong chi đoàn: ' . $branchActiveMembersCount . '. Số tiền: ' . number_format(round($branchRevenueAmount, 2), 0, ',', '.') . ' VNĐ',
                            'amount' => round($branchRevenueAmount, 2),
                            'type' => 0, // Revenue
                            'due_date' => $transaction->due_date,
                            'created_by' => $branch->secretary, // Cán bộ đoàn
                        ]);
                    }
                }
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Đã tạo khoản thu chi thành công.'
            ]);
            TransactionUpdated::dispatch($transaction);
        }

        $this->closeCreateForm();
    }

    public function sendNotification(int $transactionId): void
    {
        $transaction = Transaction::findOrFail($transactionId);

        // Chỉ cho phép gửi thông báo cho transactions do mình tạo
        abort_unless($transaction->created_by === Auth::id(), 403);
        $memberTransactions = $transaction->memberTransactions()->with('member.user')->get();

        // Calculate amount per member: tổng số tiền / tổng số thành viên
        $totalMembersCount = $memberTransactions->count();
        $amountPerMember = $totalMembersCount > 0 ? $transaction->amount / $totalMembersCount : 0;

        foreach ($memberTransactions as $memberTransaction) {
            if ($memberTransaction->member->user_id) {
                Notification::create([
                    'title' => 'Thông báo thu tiền: ' . $transaction->title,
                    'content' => 'Bạn có khoản thu mới cần thanh toán. Số tiền: ' . number_format(round($amountPerMember, 2), 0, ',', '.') . ' VNĐ',
                    'date_sent' => now(),
                    'sender_id' => Auth::id(),
                    'receiver_id' => $memberTransaction->member->user_id,
                    'notify_type' => 2, // Financial notification
                ]);
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Đã gửi thông báo đến các thành viên.'
        ]);
    }

    public function confirmPayment(int $memberTransactionId): void
    {
        $memberTransaction = MemberTransaction::findOrFail($memberTransactionId);

        // Chỉ cho phép xác nhận thanh toán cho transactions do mình tạo
        abort_unless($memberTransaction->transaction->created_by === Auth::id(), 403);

        $memberTransaction->update([
            'payment_status' => 2, // Confirmed
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Đã xác nhận thanh toán.'
        ]);
        TransactionUpdated::dispatch($memberTransaction->transaction);
    }

    public function closeTransaction(int $id): void
    {
        $transaction = Transaction::findOrFail($id);

        // Chỉ cho phép đóng transactions do mình tạo
        abort_unless($transaction->created_by === Auth::id(), 403);

        $transaction->update(['status' => 1]);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Đã đóng khoản thu chi.'
        ]);
        TransactionUpdated::dispatch(Transaction::find($id));
    }

    public function openTransaction(int $id): void
    {
        $transaction = Transaction::findOrFail($id);

        // Chỉ cho phép mở transactions do mình tạo
        abort_unless($transaction->created_by === Auth::id(), 403);

        $transaction->update(['status' => 0]);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Đã mở lại khoản thu chi.'
        ]);
        TransactionUpdated::dispatch(Transaction::find($id));
    }

    public function deleteTransaction(): void
    {
        if ($this->deletingId) {
            $transaction = Transaction::findOrFail($this->deletingId);

            // Chỉ cho phép xóa transactions do mình tạo
            abort_unless($transaction->created_by === Auth::id(), 403);

            $transaction->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Đã xóa khoản thu chi.'
            ]);
            TransactionUpdated::dispatch($transaction); // Although deleted, we might want to notify to remove from list
            $this->closeDeleteModal();
        }
    }

    public function exportExcel()
    {
        return Excel::download(new TransactionsExport, 'transactions.xlsx');
    }

    #[Computed]
    public function typeOptions()
    {
        return [
            ['value' => 0, 'label' => 'Thu'],
            ['value' => 1, 'label' => 'Chi'],
        ];
    }

    #[Computed]
    public function branches()
    {
        return Branch::orderBy('branch_name')->get();
    }

    private function resetForm(): void
    {
        $this->title = '';
        $this->description = '';
        $this->amount = '';
        $this->type = 0;
        $this->due_date = '';
    }

    public function render()
    {
        $viewingTransaction = null;
        if ($this->viewingId) {
            $viewingTransaction = Transaction::withCount([
                'memberTransactions as paid_count' => function ($query) {
                    $query->where('payment_status', '>=', 1);
                }
            ])->find($this->viewingId);

            if ($viewingTransaction) {
                // Attach filtered and ordered member transactions
                $viewingTransaction->filteredMemberTransactions = $viewingTransaction->memberTransactions()
                    ->with(['member.user'])
                    ->when($this->memberSearch, function ($query) {
                        $query->whereHas('member', function ($mq) {
                            $mq->where('full_name', 'like', '%' . $this->memberSearch . '%')
                                ->orWhereHas('user', function ($uq) {
                                    $uq->where('student_code', 'like', '%' . $this->memberSearch . '%');
                                });
                        });
                    })
                    ->when($this->classSearch, function ($query) {
                        $query->whereHas('member', function ($mq) {
                            $mq->where('branch_id', $this->classSearch);
                        });
                    })
                    ->orderByDesc('payment_status') // Confirmed/Pending first
                    ->orderBy('member_id')
                    ->get();
            }
        }

        return view('livewire.admin.manage-transactions', [
            'transactions' => Transaction::query()
                ->with(['creator', 'memberTransactions'])
                ->where('created_by', Auth::id()) // Chỉ hiển thị transactions do super admin này tạo
                ->withCount([
                    'memberTransactions as paid_count' => function ($query) {
                        $query->where('payment_status', '>=', 1);
                    },
                    'memberTransactions as total_members'
                ])
                ->when($this->search, function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->orderByDesc('created_at')
                ->paginate($this->perPage)
                ->withQueryString(),
            'viewingTransaction' => $viewingTransaction,
        ]);
    }
    public function getListeners(): array
    {
        return [
            'echo:transactions,transaction.updated' => '$refresh',
            'transaction-created' => '$refresh',
            'transaction-updated' => '$refresh',
            'transaction-deleted' => '$refresh',
            'transaction-closed' => '$refresh',
            'payment-confirmed' => '$refresh',
        ];
    }
}
