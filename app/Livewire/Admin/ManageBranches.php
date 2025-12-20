<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BranchesExport;

class ManageBranches extends Component
{
  use WithPagination;

  protected $paginationTheme = 'tailwind';

  public string $search = '';
  public int $perPage = 10;
  public bool $showBranchModal = false;
  public bool $showDeleteModal = false;
  public ?int $deletingId = null;
  public ?int $branchId = null;
  public string $mode = '';

  // Form fields
  public string $branch_name = '';
  public ?int $secretary = null;
  public string $description = '';
  public ?int $members_count = null;

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

  public function openBranchModal(string $mode, ?int $id = null): void
  {
    $this->mode = $mode;
    $this->branchId = $id;

    if ($mode === 'create') {
      $this->resetForm();
    } elseif ($mode === 'edit' || $mode === 'view') {
      $branch = Branch::withCount('members')->findOrFail($id);
      $this->branch_name = $branch->branch_name;
      $this->secretary = $branch->secretary;
      $this->description = $branch->description ?? '';
      if ($mode === 'view') {
        $this->members_count = $branch->members_count;
      }
    }

    $this->showBranchModal = true;
  }

  public function closeBranchModal(): void
  {
    $this->showBranchModal = false;
    $this->resetForm();
    $this->mode = '';
    $this->branchId = null;
  }

  public function switchToEdit(): void
  {
    $this->mode = 'edit';
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

  public function saveBranch(): void
  {
    $rules = [
      'branch_name' => ['required', 'string', 'max:255'],
      'secretary' => ['nullable', 'exists:users,id'],
      'description' => ['nullable', 'string'],
    ];

    if ($this->mode === 'edit') {
      $rules['branch_name'][] = 'unique:branches,branch_name,' . $this->branchId;
    } else {
      $rules['branch_name'][] = 'unique:branches,branch_name';
    }

    $this->validate($rules);

    $data = [
      'branch_name' => $this->branch_name,
      'secretary' => $this->secretary ?: null,
      'description' => $this->description ?: null,
    ];

    if ($this->mode === 'edit') {
      Branch::findOrFail($this->branchId)->update($data);
      $this->dispatch('branch-updated');
    } else {
      Branch::create($data);
      $this->dispatch('branch-created');
    }

    $this->closeBranchModal();
  }

  public function deleteBranch(): void
  {
    if ($this->deletingId) {
      // Check if branch has any members
      $branch = Branch::findOrFail($this->deletingId);
      if ($branch->members()->count() > 0) {
        $this->addError('delete', 'Không thể xóa chi đoàn đang có thành viên.');
        return;
      }

      $branch->delete();
      $this->dispatch('branch-deleted');
      $this->closeDeleteModal();
    }
  }

  public function exportExcel()
  {
    return Excel::download(new BranchesExport, 'branches.xlsx');
  }

  #[Computed]
  public function secretaryOptions()
  {
    return User::select('id', 'student_code')
      ->orderBy('id')
      ->get()
      ->map(fn($u) => [
        'value' => $u->id,
        'label' => $u->student_code . ' (' . ($u->full_name ?? "Chưa có thông tin") . ')'
      ])->toArray();
  }

  private function resetForm(): void
  {
    $this->branch_name = '';
    $this->secretary = null;
    $this->description = '';
    $this->members_count = null;
  }

  public function render()
  {
    return view('livewire.admin.manage-branches', [
      'branches' => Branch::query()
        ->with('secretary')
        ->withCount('members')
        ->when($this->search, function ($query) {
          $query->where('branch_name', 'like', '%' . $this->search . '%');
        })
        ->orderByDesc('id')
        ->paginate($this->perPage)
        ->withQueryString(),
      'users' => [],
    ]);
  }
}