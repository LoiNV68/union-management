<?php

namespace App\Livewire\Union;

use App\Models\Transaction;
use App\Models\MemberTransaction;
use App\Models\Branch;
use App\Events\TransactionUpdated;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ManageBranchTransactions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $memberSearch = '';
    public int $perPage = 10;
    public bool $showViewModal = false;
    public bool $showPayConfirmModal = false;
    public ?int $viewingId = null;
    public ?int $payingTransactionId = null;

    public function mount(): void
    {
        abort_unless(Auth::user()?->role === 1, 403);
        
        // Check if user is a branch secretary
        $user = Auth::user();
        $branches = Branch::where('secretary', $user->id)->get();
        
        abort_unless($branches->count() > 0, 403, 'Bạn không phải là cán bộ đoàn của bất kỳ chi đoàn nào.');
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
    }

    public function openPayConfirmModal(int $transactionId): void
    {
        $this->payingTransactionId = $transactionId;
        $this->showPayConfirmModal = true;
    }

    public function closePayConfirmModal(): void
    {
        $this->showPayConfirmModal = false;
        $this->payingTransactionId = null;
    }

    public function confirmPayment(int $memberTransactionId): void
    {
        $memberTransaction = MemberTransaction::findOrFail($memberTransactionId);
        
        // Check if member belongs to user's branch
        $userBranches = Branch::where('secretary', Auth::id())->pluck('id');
        if (!$userBranches->contains($memberTransaction->member->branch_id)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Bạn không có quyền xác nhận thanh toán này.'
            ]);
            return;
        }
        
        $memberTransaction->update([
            'payment_status' => 2, // Confirmed
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Đã xác nhận thanh toán.'
        ]);
    }

    public function confirmPayToManagement(): void
    {
        if (!$this->payingTransactionId) {
            return;
        }
        
        $this->payToManagement($this->payingTransactionId);
        $this->closePayConfirmModal();
    }

    public function payToManagement(int $transactionId): void
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        // Chỉ cho phép thanh toán cho transactions do mình tạo
        abort_unless($transaction->created_by === Auth::id(), 403);
        
        $userBranches = $this->userBranches;
        $branchIds = $this->branchIds;

        // Check if transaction is revenue (type 0)
        if ($transaction->type !== 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Chỉ có thể thanh toán cho khoản thu.'
            ]);
            return;
        }

        // Check if expense transaction already exists for this revenue transaction
        $existingExpense = Transaction::where('type', 1)
            ->where('description', 'like', '%' . $transaction->title . '%')
            ->where(function ($query) use ($userBranches) {
                foreach ($userBranches as $branch) {
                    $query->orWhere('title', 'like', 'Chi cho quản lý từ ' . $branch->branch_name . '%');
                }
            })
            ->first();

        if ($existingExpense) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Khoản chi cho quản lý đã được tạo trước đó.'
            ]);
            return;
        }

        // Create expense transaction for each branch based on paid members
        $createdCount = 0;
        foreach ($userBranches as $branch) {
            $branchPaidTransactions = $transaction->memberTransactions()
                ->whereHas('member', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->where('payment_status', 2)
                ->count();

            $branchMemberCount = $transaction->memberTransactions()
                ->whereHas('member', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->count();

            if ($branchMemberCount > 0 && $branchPaidTransactions > 0) {
                // Calculate amount per member: số tiền super admin tạo / tổng số thành viên trong hệ thống
                $totalMembersCount = $transaction->memberTransactions()->count();
                if ($totalMembersCount === 0) {
                    continue;
                }
                
                // Số tiền mỗi thành viên phải thanh toán
                $amountPerMember = $transaction->amount / $totalMembersCount;
                
                // Số tiền chi đoàn phải trả = số thành viên trong chi đoàn * số tiền mỗi thành viên
                // KHÔNG phải là số tiền mà super admin tạo ra
                $branchTotalAmount = $amountPerMember * $branchMemberCount;

                echo('Số tiền mỗi thành viên phải thanh toán: ' . $amountPerMember);
                echo('Số tiền chi đoàn phải trả: ' . $branchTotalAmount);

                // Check if expense already exists for this branch and transaction
                $existingExpense = Transaction::where('type', 1)
                    ->where('title', 'like', 'Chi cho quản lý từ ' . $branch->branch_name . ' - ' . $transaction->title . '%')
                    ->first();

                if (!$existingExpense) {
                    Transaction::create([
                        'title' => 'Chi cho quản lý từ ' . $branch->branch_name . ' - ' . $transaction->title,
                        'description' => 'Khoản chi từ khoản thu: ' . $transaction->title . '. Chi đoàn: ' . $branch->branch_name . '. Số thành viên trong chi đoàn: ' . $branchMemberCount . '. Số tiền: ' . number_format(round($branchTotalAmount, 2), 0, ',', '.') . ' VNĐ',
                        'amount' => round($branchTotalAmount, 2),
                        'type' => 1, // Expense
                        'due_date' => $transaction->due_date,
                        'created_by' => Auth::id(),
                    ]);
                    $createdCount++;
                }
            }
        }

        if ($createdCount === 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Chưa có thành viên nào thanh toán để tạo khoản chi.'
            ]);
            return;
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Đã tạo khoản chi cho quản lý thành công.'
        ]);
        
        // Dispatch event to refresh transactions
        TransactionUpdated::dispatch($transaction);
    }

    public function isAllMembersPaid(int $transactionId, int $branchId): bool
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        $totalMembers = $transaction->memberTransactions()
            ->whereHas('member', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->count();

        $paidMembers = $transaction->memberTransactions()
            ->whereHas('member', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('payment_status', 2)
            ->count();

        return $totalMembers > 0 && $paidMembers === $totalMembers;
    }

    #[Computed]
    public function userBranches()
    {
        return Branch::where('secretary', Auth::id())->get();
    }

    #[Computed]
    public function branchIds()
    {
        return $this->userBranches->pluck('id')->toArray();
    }

    public function render()
    {
        $userBranches = $this->userBranches;
        $branchIds = $this->branchIds;

        $viewingTransaction = null;
        if ($this->viewingId) {
            $viewingTransaction = Transaction::find($this->viewingId);
            
            $branchesPaymentStatus = [];
            
            if ($viewingTransaction) {
                // Check if this is a transaction created specifically for branch officer
                // (title starts with "Thu từ:" - created automatically when super admin creates revenue)
                $isBranchSpecificTransaction = str_starts_with($viewingTransaction->title, 'Thu từ:');
                
                // Find the parent transaction (original transaction from super admin)
                $parentTransaction = null;
                if ($isBranchSpecificTransaction) {
                    // Extract the original transaction title from "Thu từ: [Original Title] - [Branch Name]"
                    $titleParts = explode(' - ', $viewingTransaction->title);
                    if (count($titleParts) > 0) {
                        $originalTitle = str_replace('Thu từ: ', '', $titleParts[0]);
                        // Find the original transaction by title created by super admin (role = 2)
                        $parentTransaction = Transaction::where('title', $originalTitle)
                            ->whereHas('creator', function ($q) {
                                $q->where('role', 2); // Created by super admin
                            })
                            ->where('type', 0)
                            ->orderByDesc('created_at')
                            ->first();
                    }
                }
                
                // Use parent transaction for calculations if this is a branch-specific transaction
                $transactionForCalculation = $parentTransaction ?: $viewingTransaction;
                
                // Only process member transactions for revenue transactions (type = 0)
                if ($viewingTransaction->type === 0) {
                    // Only show members from user's branches
                    $viewingTransaction->filteredMemberTransactions = $transactionForCalculation->memberTransactions()
                        ->whereHas('member', function ($query) use ($branchIds) {
                            $query->whereIn('branch_id', $branchIds);
                        })
                        ->with(['member.user', 'member.branch'])
                        ->when($this->memberSearch, function ($query) {
                            $query->whereHas('member', function ($mq) {
                                $mq->where('full_name', 'like', '%' . $this->memberSearch . '%')
                                    ->orWhereHas('user', function ($uq) {
                                        $uq->where('student_code', 'like', '%' . $this->memberSearch . '%');
                                    });
                            });
                        })
                        ->orderByDesc('payment_status')
                        ->orderBy('member_id')
                        ->get();

                    // Calculate paid count and total count
                    $viewingTransaction->paid_count = $viewingTransaction->filteredMemberTransactions
                        ->where('payment_status', '>=', 1)
                        ->count();
                    
                    $viewingTransaction->total_members = $viewingTransaction->filteredMemberTransactions->count();

                    // Check if all members in each branch have paid
                    foreach ($userBranches as $branch) {
                        $totalMembers = $transactionForCalculation->memberTransactions()
                            ->whereHas('member', function ($q) use ($branch) {
                                $q->where('branch_id', $branch->id);
                            })
                            ->count();

                        $paidMembers = $transactionForCalculation->memberTransactions()
                            ->whereHas('member', function ($q) use ($branch) {
                                $q->where('branch_id', $branch->id);
                            })
                            ->where('payment_status', 2)
                            ->count();

                        $branchesPaymentStatus[$branch->id] = [
                            'branch' => $branch,
                            'all_paid' => $totalMembers > 0 && $paidMembers === $totalMembers,
                            'paid_count' => $paidMembers,
                            'total_count' => $totalMembers,
                        ];
                    }
                } else {
                    // For expense transactions (type = 1), set empty collection
                    $viewingTransaction->filteredMemberTransactions = collect([]);
                    $viewingTransaction->paid_count = 0;
                    $viewingTransaction->total_members = 0;
                }
            }
        }

        // Chỉ hiển thị transactions được tạo bởi cán bộ đoàn này
        $transactionsQuery = Transaction::query()
            ->with(['creator'])
            ->where('created_by', Auth::id()) // Chỉ hiển thị transactions do cán bộ đoàn này tạo
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            });
        
        $transactions = $transactionsQuery->orderByDesc('created_at')
            ->paginate($this->perPage)
            ->withQueryString();
        
        // Tính toán paid_count và total_members cho mỗi transaction
        $transactions->getCollection()->transform(function ($transaction) use ($branchIds) {
                // Check if this is a branch-specific transaction (title starts with "Thu từ:")
                $isBranchSpecificTransaction = str_starts_with($transaction->title, 'Thu từ:');
                
                if ($isBranchSpecificTransaction && $transaction->type === 0) {
                    // Find parent transaction
                    $titleParts = explode(' - ', $transaction->title);
                    if (count($titleParts) > 0) {
                        $originalTitle = str_replace('Thu từ: ', '', $titleParts[0]);
                        $parentTransaction = Transaction::where('title', $originalTitle)
                            ->whereHas('creator', function ($q) {
                                $q->where('role', 2); // Created by super admin
                            })
                            ->where('type', 0)
                            ->orderByDesc('created_at')
                            ->first();
                        
                        if ($parentTransaction) {
                            $transaction->paid_count = $parentTransaction->memberTransactions()
                                ->whereHas('member', function ($q) use ($branchIds) {
                                    $q->whereIn('branch_id', $branchIds);
                                })
                                ->where('payment_status', '>=', 1)
                                ->count();
                            
                            $transaction->total_members = $parentTransaction->memberTransactions()
                                ->whereHas('member', function ($q) use ($branchIds) {
                                    $q->whereIn('branch_id', $branchIds);
                                })
                                ->count();
                        } else {
                            $transaction->paid_count = 0;
                            $transaction->total_members = 0;
                        }
                    } else {
                        $transaction->paid_count = 0;
                        $transaction->total_members = 0;
                    }
                } else {
                    // For transactions that have member_transactions, use withCount results
                    $transaction->paid_count = $transaction->memberTransactions()
                        ->whereHas('member', function ($q) use ($branchIds) {
                            $q->whereIn('branch_id', $branchIds);
                        })
                        ->where('payment_status', '>=', 1)
                        ->count();
                    
                    $transaction->total_members = $transaction->memberTransactions()
                        ->whereHas('member', function ($q) use ($branchIds) {
                            $q->whereIn('branch_id', $branchIds);
                        })
                        ->count();
                }
                
                return $transaction;
            });

        return view('livewire.union.manage-branch-transactions', [
            'transactions' => $transactions,
            'viewingTransaction' => $viewingTransaction,
            'userBranches' => $userBranches,
            'branchesPaymentStatus' => $branchesPaymentStatus ?? [],
        ]);
    }

    public function getListeners(): array
    {
        return [
            'echo:transactions,transaction.updated' => '$refresh',
            'transaction-created' => '$refresh',
            'transaction-updated' => '$refresh',
            'transaction-deleted' => '$refresh',
            'payment-confirmed' => '$refresh',
        ];
    }
}

