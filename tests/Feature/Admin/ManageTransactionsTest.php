<?php

use App\Models\User;
use App\Models\Branch;
use App\Models\Member;
use App\Models\Transaction;
use App\Livewire\Admin\ManageTransactions;
use Livewire\Livewire;

test('super admin creating revenue transaction automatically creates expense for each branch', function () {
    // Create super admin user (role 2)
    $superAdmin = User::factory()->create(['role' => 2]);

    // Create branches
    $branch1 = Branch::factory()->create(['branch_name' => 'D11.01.01']);
    $branch2 = Branch::factory()->create(['branch_name' => 'D11.01.02']);

    // Create active members for branch1 (3 members)
    $members1 = Member::factory()->count(3)->create([
        'branch_id' => $branch1->id,
        'status' => 1,
    ]);

    // Create active members for branch2 (2 members)
    $members2 = Member::factory()->count(2)->create([
        'branch_id' => $branch2->id,
        'status' => 1,
    ]);

    // Create one inactive member for branch2 (should not be counted)
    Member::factory()->create([
        'branch_id' => $branch2->id,
        'status' => 0,
    ]);

    // Act: Create a revenue transaction as super admin
    Livewire::actingAs($superAdmin)
        ->test(ManageTransactions::class)
        ->set('title', 'Khoản thu tháng 1')
        ->set('description', 'Thu tiền đóng quỹ')
        ->set('amount', 100000)
        ->set('type', 0) // Revenue
        ->set('due_date', now()->addDays(30)->format('Y-m-d'))
        ->call('saveTransaction');

    // Assert: Revenue transaction was created
    $revenueTransaction = Transaction::where('type', 0)
        ->where('title', 'Khoản thu tháng 1')
        ->first();

    expect($revenueTransaction)->not->toBeNull();
    expect($revenueTransaction->amount)->toBe(100000.0);
    expect($revenueTransaction->created_by)->toBe($superAdmin->id);

    // Assert: Expense transactions were created for each branch
    $expenseTransactions = Transaction::where('type', 1)
        ->where('title', 'like', 'Chi cho cán bộ đoàn:%')
        ->get();

    expect($expenseTransactions)->toHaveCount(2);

    // Assert: Branch1 expense has amount = 3 (number of active members)
    $branch1Expense = $expenseTransactions->firstWhere('title', 'Chi cho cán bộ đoàn: ' . $branch1->branch_name);
    expect($branch1Expense)->not->toBeNull();
    expect($branch1Expense->amount)->toBe(3.0);
    expect($branch1Expense->created_by)->toBe($superAdmin->id);
    expect($branch1Expense->description)->toContain('Khoản chi tự động tạo từ khoản thu: Khoản thu tháng 1');
    expect($branch1Expense->description)->toContain('Số lượng thành viên: 3');

    // Assert: Branch2 expense has amount = 2 (number of active members)
    $branch2Expense = $expenseTransactions->firstWhere('title', 'Chi cho cán bộ đoàn: ' . $branch2->branch_name);
    expect($branch2Expense)->not->toBeNull();
    expect($branch2Expense->amount)->toBe(2.0);
    expect($branch2Expense->created_by)->toBe($superAdmin->id);
    expect($branch2Expense->description)->toContain('Khoản chi tự động tạo từ khoản thu: Khoản thu tháng 1');
    expect($branch2Expense->description)->toContain('Số lượng thành viên: 2');
});

test('admin (role 1) creating revenue transaction does not create expense transactions', function () {
    // Create admin user (role 1)
    $admin = User::factory()->create(['role' => 1]);

    // Create branch with members
    $branch = Branch::factory()->create();
    Member::factory()->count(2)->create([
        'branch_id' => $branch->id,
        'status' => 1,
    ]);

    // Act: Create a revenue transaction as admin (not super admin)
    Livewire::actingAs($admin)
        ->test(ManageTransactions::class)
        ->set('title', 'Khoản thu tháng 1')
        ->set('amount', 100000)
        ->set('type', 0)
        ->call('saveTransaction');

    // Assert: Revenue transaction was created
    $revenueTransaction = Transaction::where('type', 0)
        ->where('title', 'Khoản thu tháng 1')
        ->first();

    expect($revenueTransaction)->not->toBeNull();

    // Assert: NO expense transactions were created
    $expenseTransactions = Transaction::where('type', 1)
        ->where('title', 'like', 'Chi cho cán bộ đoàn:%')
        ->get();

    expect($expenseTransactions)->toHaveCount(0);
});

test('creating expense transaction does not trigger automatic expense creation', function () {
    // Create super admin user
    $superAdmin = User::factory()->create(['role' => 2]);

    // Create branch with members
    $branch = Branch::factory()->create();
    Member::factory()->count(2)->create([
        'branch_id' => $branch->id,
        'status' => 1,
    ]);

    // Act: Create an expense transaction (type 1)
    Livewire::actingAs($superAdmin)
        ->test(ManageTransactions::class)
        ->set('title', 'Khoản chi tháng 1')
        ->set('amount', 50000)
        ->set('type', 1) // Expense, not revenue
        ->call('saveTransaction');

    // Assert: Expense transaction was created
    $expenseTransaction = Transaction::where('type', 1)
        ->where('title', 'Khoản chi tháng 1')
        ->first();

    expect($expenseTransaction)->not->toBeNull();

    // Assert: NO additional automatic expense transactions were created
    $autoExpenseTransactions = Transaction::where('type', 1)
        ->where('title', 'like', 'Chi cho cán bộ đoàn:%')
        ->get();

    expect($autoExpenseTransactions)->toHaveCount(0);
});

test('branch with no active members does not get expense transaction', function () {
    // Create super admin user
    $superAdmin = User::factory()->create(['role' => 2]);

    // Create branch with no active members
    $branchWithNoMembers = Branch::factory()->create(['branch_name' => 'D11.01.03']);

    // Create branch with active members
    $branchWithMembers = Branch::factory()->create(['branch_name' => 'D11.01.04']);
    Member::factory()->count(2)->create([
        'branch_id' => $branchWithMembers->id,
        'status' => 1,
    ]);

    // Act: Create a revenue transaction
    Livewire::actingAs($superAdmin)
        ->test(ManageTransactions::class)
        ->set('title', 'Khoản thu tháng 1')
        ->set('amount', 100000)
        ->set('type', 0)
        ->call('saveTransaction');

    // Assert: Only expense transaction for branch with members was created
    $expenseTransactions = Transaction::where('type', 1)
        ->where('title', 'like', 'Chi cho cán bộ đoàn:%')
        ->get();

    expect($expenseTransactions)->toHaveCount(1);
    expect($expenseTransactions->first()->title)->toBe('Chi cho cán bộ đoàn: ' . $branchWithMembers->branch_name);
});
