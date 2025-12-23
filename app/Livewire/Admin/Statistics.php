<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use App\Models\MemberTransaction;
use App\Models\Member;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Statistics extends Component
{
    public string $period = 'all'; // 'all', 'month', 'year'
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $selectedBranchId = null;
    public bool $showHeader = true;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, [1, 2]), 403);

        if ($user->role === 1) {
            $this->selectedBranchId = $user->member?->branch_id;
        }
    }

    #[Computed]
    public function branches()
    {
        $user = Auth::user();
        if ($user->role === 1) {
            $branchId = $user->member?->branch_id;
            return Branch::where('id', $branchId)->get();
        }
        return Branch::orderBy('branch_name')->get();
    }

    #[Computed]
    public function summaryStatistics()
    {
        $query = Transaction::query();

        // Filter by date range if provided
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
        } elseif ($this->period === 'month') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        } elseif ($this->period === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        // Filter by branch if selected
        if ($this->selectedBranchId) {
            $query->whereHas('memberTransactions.member', function ($q) {
                $q->where('branch_id', $this->selectedBranchId);
            });
        }

        // Only show revenue transactions and expenses created by super admin
        $query->where(function ($q) {
            $q->where('type', 0)
                ->orWhere(function ($q2) {
                    $q2->where('type', 1)
                        ->whereHas('creator', function ($userQuery) {
                            $userQuery->where('role', 2);
                        });
                });
        });

        $totalRevenue = Transaction::query()
            ->where('type', 0)
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
            })
            ->when($this->period === 'month', function ($q) {
                $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            })
            ->when($this->period === 'year', function ($q) {
                $q->whereYear('created_at', now()->year);
            })
            ->when($this->selectedBranchId, function ($q) {
                $q->whereHas('memberTransactions.member', function ($q2) {
                    $q2->where('branch_id', $this->selectedBranchId);
                });
            })
            ->sum('amount');

        // Calculate actual collected revenue (based on paid members)
        // Số tiền đã thu = tổng (số tiền mỗi transaction / tổng số thành viên) * số thành viên đã thanh toán
        $paidMemberTransactions = MemberTransaction::query()
            ->where('payment_status', 2)
            ->with('transaction')
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereHas('transaction', function ($q2) {
                    $q2->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
                });
            })
            ->when($this->period === 'month', function ($q) {
                $q->whereHas('transaction', function ($q2) {
                    $q2->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                });
            })
            ->when($this->period === 'year', function ($q) {
                $q->whereHas('transaction', function ($q2) {
                    $q2->whereYear('created_at', now()->year);
                });
            })
            ->when($this->selectedBranchId, function ($q) {
                $q->whereHas('member', function ($q2) {
                    $q2->where('branch_id', $this->selectedBranchId);
                });
            })
            ->whereHas('transaction', function ($q) {
                $q->where('type', 0);
            })
            ->get();

        $actualRevenue = 0;
        $groupedByTransaction = $paidMemberTransactions->groupBy('transaction_id');

        foreach ($groupedByTransaction as $transactionId => $memberTransactions) {
            $transaction = $memberTransactions->first()->transaction;
            $totalMembersInTransaction = $transaction->memberTransactions()->count();

            if ($totalMembersInTransaction > 0) {
                $amountPerMember = $transaction->amount / $totalMembersInTransaction;
                $actualRevenue += $amountPerMember * $memberTransactions->count();
            }
        }

        $totalExpense = Transaction::query()
            ->where('type', 1)
            ->whereHas('creator', function ($q) {
                $q->where('role', 2);
            })
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
            })
            ->when($this->period === 'month', function ($q) {
                $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            })
            ->when($this->period === 'year', function ($q) {
                $q->whereYear('created_at', now()->year);
            })
            ->sum('amount');

        $totalTransactions = $query->count();
        $totalMembers = Member::where('status', 1)
            ->when($this->selectedBranchId, function ($q) {
                $q->where('branch_id', $this->selectedBranchId);
            })
            ->count();

        $totalMemberTransactions = MemberTransaction::query()
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                    ->whereBetween('transactions.created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
            })
            ->when($this->period === 'month', function ($q) {
                $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                    ->whereMonth('transactions.created_at', now()->month)
                    ->whereYear('transactions.created_at', now()->year);
            })
            ->when($this->period === 'year', function ($q) {
                $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                    ->whereYear('transactions.created_at', now()->year);
            })
            ->when($this->selectedBranchId, function ($q) {
                $q->join('members', 'member_transactions.member_id', '=', 'members.id')
                    ->where('members.branch_id', $this->selectedBranchId);
            })
            ->count();

        $paidTransactions = MemberTransaction::query()
            ->where('payment_status', 2)
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                    ->whereBetween('transactions.created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
            })
            ->when($this->period === 'month', function ($q) {
                $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                    ->whereMonth('transactions.created_at', now()->month)
                    ->whereYear('transactions.created_at', now()->year);
            })
            ->when($this->period === 'year', function ($q) {
                $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                    ->whereYear('transactions.created_at', now()->year);
            })
            ->when($this->selectedBranchId, function ($q) {
                $q->join('members', 'member_transactions.member_id', '=', 'members.id')
                    ->where('members.branch_id', $this->selectedBranchId);
            })
            ->count();

        $paymentRate = $totalMemberTransactions > 0
            ? round(($paidTransactions / $totalMemberTransactions) * 100, 2)
            : 0;

        return [
            'total_revenue' => $totalRevenue,
            'actual_revenue' => $actualRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $actualRevenue - $totalExpense,
            'total_transactions' => $totalTransactions,
            'total_members' => $totalMembers,
            'paid_transactions' => $paidTransactions,
            'total_member_transactions' => $totalMemberTransactions,
            'payment_rate' => $paymentRate,
        ];
    }

    #[Computed]
    public function branchStatistics()
    {
        $branches = Branch::withCount([
            'members as active_members_count' => function ($q) {
                $q->where('status', 1);
            }
        ])->get();

        $statistics = [];
        foreach ($branches as $branch) {
            $totalMembers = $branch->active_members_count;

            if ($totalMembers === 0) {
                continue;
            }

            // Calculate branch revenue from paid members
            $branchPaidTransactions = MemberTransaction::query()
                ->whereHas('member', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->where('payment_status', 2)
                ->with('transaction')
                ->when($this->startDate && $this->endDate, function ($q) {
                    $q->whereHas('transaction', function ($q2) {
                        $q2->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
                    });
                })
                ->when($this->period === 'month', function ($q) {
                    $q->whereHas('transaction', function ($q2) {
                        $q2->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                    });
                })
                ->when($this->period === 'year', function ($q) {
                    $q->whereHas('transaction', function ($q2) {
                        $q2->whereYear('created_at', now()->year);
                    });
                })
                ->whereHas('transaction', function ($q) {
                    $q->where('type', 0);
                })
                ->get();

            $branchRevenue = 0;
            $groupedBranchTransactions = $branchPaidTransactions->groupBy('transaction_id');

            foreach ($groupedBranchTransactions as $transactionId => $memberTransactions) {
                $transaction = $memberTransactions->first()->transaction;
                $totalMembersInTransaction = $transaction->memberTransactions()->count();

                if ($totalMembersInTransaction > 0) {
                    $amountPerMember = $transaction->amount / $totalMembersInTransaction;
                    $branchRevenue += $amountPerMember * $memberTransactions->count();
                }
            }

            $branchTransactions = MemberTransaction::query()
                ->whereHas('member', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->when($this->startDate && $this->endDate, function ($q) {
                    $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                        ->whereBetween('transactions.created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
                })
                ->when($this->period === 'month', function ($q) {
                    $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                        ->whereMonth('transactions.created_at', now()->month)
                        ->whereYear('transactions.created_at', now()->year);
                })
                ->when($this->period === 'year', function ($q) {
                    $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                        ->whereYear('transactions.created_at', now()->year);
                })
                ->count();

            $branchPaidTransactions = MemberTransaction::query()
                ->whereHas('member', function ($q) use ($branch) {
                    $q->where('branch_id', $branch->id);
                })
                ->where('payment_status', 2)
                ->when($this->startDate && $this->endDate, function ($q) {
                    $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                        ->whereBetween('transactions.created_at', [$this->startDate, $this->endDate . ' 23:59:59']);
                })
                ->when($this->period === 'month', function ($q) {
                    $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                        ->whereMonth('transactions.created_at', now()->month)
                        ->whereYear('transactions.created_at', now()->year);
                })
                ->when($this->period === 'year', function ($q) {
                    $q->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
                        ->whereYear('transactions.created_at', now()->year);
                })
                ->count();

            $branchPaymentRate = $branchTransactions > 0
                ? round(($branchPaidTransactions / $branchTransactions) * 100, 2)
                : 0;

            $statistics[] = [
                'branch' => $branch,
                'total_members' => $totalMembers,
                'revenue' => $branchRevenue,
                'transactions' => $branchTransactions,
                'paid_transactions' => $branchPaidTransactions,
                'payment_rate' => $branchPaymentRate,
            ];
        }

        return collect($statistics)->sortByDesc('revenue');
    }

    #[Computed]
    public function monthlyRevenueData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('m/Y');

            // Calculate monthly revenue from paid members
            $monthPaidTransactions = MemberTransaction::query()
                ->where('payment_status', 2)
                ->with('transaction')
                ->whereHas('transaction', function ($q) use ($date) {
                    $q->where('type', 0)
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year);
                })
                ->when($this->selectedBranchId, function ($q) {
                    $q->whereHas('member', function ($q2) {
                        $q2->where('branch_id', $this->selectedBranchId);
                    });
                })
                ->get();

            $revenue = 0;
            $groupedMonthTransactions = $monthPaidTransactions->groupBy('transaction_id');

            foreach ($groupedMonthTransactions as $transactionId => $memberTransactions) {
                $transaction = $memberTransactions->first()->transaction;
                $totalMembersInTransaction = $transaction->memberTransactions()->count();

                if ($totalMembersInTransaction > 0) {
                    $amountPerMember = $transaction->amount / $totalMembersInTransaction;
                    $revenue += $amountPerMember * $memberTransactions->count();
                }
            }

            $expense = Transaction::query()
                ->where('type', 1)
                ->whereHas('creator', function ($q) {
                    $q->where('role', 2);
                })
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->when($this->selectedBranchId, function ($q) {
                    $q->whereHas('memberTransactions.member', function ($q2) {
                        $q2->where('branch_id', $this->selectedBranchId);
                    });
                })
                ->sum('amount');

            $data[] = [
                'month' => $monthLabel,
                'revenue' => round($revenue, 2),
                'expense' => round($expense, 2),
                'net' => round($revenue - $expense, 2),
            ];
        }

        return $data;
    }

    public function updatedPeriod(): void
    {
        $this->startDate = null;
        $this->endDate = null;
    }

    public function resetFilters(): void
    {
        $this->period = 'all';
        $this->startDate = null;
        $this->endDate = null;
        $this->selectedBranchId = null;
    }

    public function render()
    {
        return view('livewire.admin.statistics');
    }
}