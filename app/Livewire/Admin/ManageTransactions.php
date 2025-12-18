<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use App\Models\MemberTransaction;
use App\Models\Member;
use App\Models\Notification;
use App\Events\TransactionUpdated;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTransactions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
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
        $this->viewingId = $id;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingId = null;
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
            'amount' => 'required|numeric|min:0',
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
            Transaction::findOrFail($this->editingId)->update($data);
            $this->dispatch('transaction-updated');
            TransactionUpdated::dispatch(Transaction::find($this->editingId));
        } else {
            $transaction = Transaction::create($data);

            // Create member_transactions for all active members
            $members = Member::where('status', 1)->get();
            foreach ($members as $member) {
                MemberTransaction::create([
                    'transaction_id' => $transaction->id,
                    'member_id' => $member->id,
                    'payment_status' => 0,
                ]);
            }

            $this->dispatch('transaction-created');
            TransactionUpdated::dispatch($transaction);
        }

        $this->closeCreateForm();
    }

    public function sendNotification(int $transactionId): void
    {
        $transaction = Transaction::findOrFail($transactionId);
        $members = $transaction->memberTransactions()->with('member.user')->get();

        foreach ($members as $memberTransaction) {
            if ($memberTransaction->member->user_id) {
                Notification::create([
                    'title' => 'Thông báo thu tiền: ' . $transaction->title,
                    'content' => 'Bạn có khoản thu mới cần thanh toán. Số tiền: ' . number_format((float) $transaction->amount, 0, ',', '.') . ' VNĐ',
                    'date_sent' => now(),
                    'sender_id' => Auth::id(),
                    'receiver_id' => $memberTransaction->member->user_id,
                    'notify_type' => 2, // Financial notification
                ]);
            }
        }

        $this->dispatch('notification-sent');
    }

    public function confirmPayment(int $memberTransactionId): void
    {
        $memberTransaction = MemberTransaction::findOrFail($memberTransactionId);
        $memberTransaction->update([
            'payment_status' => 2, // Confirmed
        ]);

        $this->dispatch('payment-confirmed');
        TransactionUpdated::dispatch($memberTransaction->transaction);
    }

    public function closeTransaction(int $id): void
    {
        Transaction::findOrFail($id)->update(['status' => 1]);
        $this->dispatch('transaction-closed');
        TransactionUpdated::dispatch(Transaction::find($id));
    }

    public function deleteTransaction(): void
    {
        if ($this->deletingId) {
            $transaction = Transaction::findOrFail($this->deletingId);
            $transaction->delete();
            $this->dispatch('transaction-deleted');
            TransactionUpdated::dispatch($transaction); // Although deleted, we might want to notify to remove from list
            $this->closeDeleteModal();
        }
    }

    #[Computed]
    public function typeOptions()
    {
        return [
            ['value' => 0, 'label' => 'Thu'],
            ['value' => 1, 'label' => 'Chi'],
        ];
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
        return view('livewire.admin.manage-transactions', [
            'transactions' => Transaction::query()
                ->with(['creator', 'memberTransactions'])
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
            'viewingTransaction' => $this->viewingId ? Transaction::with(['memberTransactions.member.user'])
                ->withCount([
                    'memberTransactions as paid_count' => function ($query) {
                        $query->where('payment_status', '>=', 1);
                    }
                ])
                ->find($this->viewingId) : null,
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
