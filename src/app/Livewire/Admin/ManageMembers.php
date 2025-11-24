<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class ManageMembers extends Component
{
  use WithPagination;

  protected $paginationTheme = 'tailwind';

  // Search & Pagination
  public string $search = '';
  public int $perPage = 10;
  public string $statusFilter = 'all';
  public ?int $branchFilter = null;

  // Modal States
  public bool $showModal = false;
  public bool $showDeleteModal = false;
  public string $modalMode = 'create'; // create, edit, view
  public ?int $editingId = null;
  public ?int $deletingId = null;

  // Form Fields
  public string $full_name = '';
  public string $birth_date = '';
  public int $gender = 0;
  public string $address = '';
  public string $email = '';
  public string $phone_number = '';
  public string $join_date;
  public int $status = 1;
  public ?int $user_id = null;
  public ?int $branch_id = null;

  protected function rules(): array
  {
    $ignore = $this->editingId ?? 'NULL';

    return [
      'full_name' => ['required', 'string', 'max:255'],
      'birth_date' => ['required', 'date', 'before:today'],
      'gender' => ['required', 'integer', 'in:0,1'],
      'address' => ['nullable', 'string', 'max:500'],
      'email' => ['required', 'email', 'max:255', "unique:members,email,{$ignore}"],
      'phone_number' => ['nullable', 'string', 'regex:/^[0-9]{10,11}$/'],
      'join_date' => ['required', 'date', 'after_or_equal:birth_date'],
      'status' => ['required', 'integer', 'in:0,1'],
      'user_id' => ['required', 'integer', 'exists:users,id', "unique:members,user_id,{$ignore}"],
      'branch_id' => ['required', 'integer', 'exists:branches,id'],
    ];
  }

  protected $messages = [
    'full_name.required' => 'Vui lòng nhập họ tên',
    'birth_date.required' => 'Vui lòng chọn ngày sinh',
    'birth_date.before' => 'Ngày sinh phải trước ngày hôm nay',
    'email.required' => 'Vui lòng nhập email',
    'email.email' => 'Email không hợp lệ',
    'email.unique' => 'Email này đã được sử dụng',
    'phone_number.regex' => 'Số điện thoại phải có 10-11 chữ số',
    'user_id.unique' => 'Tài khoản này đã được liên kết với một member khác',
    'branch_id.required' => 'Vui lòng chọn chi đoàn',
    'user_id.required' => 'Vui lòng chọn tài khoản',
    'join_date.required' => 'Vui lòng chọn ngày tham gia',
    'gender.required' => 'Vui lòng chọn giới tính',
  ];

  public function mount(): void
  {
    abort_unless(in_array(Auth::user()?->role, [1, 2]), 403);
  }

  #[Computed]
  public function branches()
  {
    return Branch::orderBy('branch_name')->get();
  }

  #[Computed]
  public function users()
  {
    $query = User::orderBy('student_code');

    if ($this->modalMode === 'create') {
      $query->whereDoesntHave('member');
    }

    return $query->get();
  }

  public function updatingSearch(): void
  {
    $this->resetPage();
  }

  public function updatingStatusFilter(): void
  {
    $this->resetPage();
  }

  public function updatingBranchFilter(): void
  {
    $this->resetPage();
  }

  public function openCreateForm(): void
  {
    $this->resetForm();
    $this->modalMode = 'create';
    $this->showModal = true;
  }

  public function openEditForm(int $id): void
  {
    $this->fillForm(Member::findOrFail($id));
    $this->modalMode = 'edit';
    $this->showModal = true;
  }

  public function openViewModal(int $id): void
  {
    $this->fillForm(Member::findOrFail($id));
    $this->modalMode = 'view';
    $this->showModal = true;
  }

  private function fillForm(Member $member): void
  {
    $this->editingId = $member->id;
    $this->full_name = $member->full_name;
    $this->birth_date = $member->birth_date->format('Y-m-d');
    $this->gender = $member->gender;
    $this->address = $member->address ?? '';
    $this->email = $member->email;
    $this->phone_number = $member->phone_number ?? '';
    $this->join_date = $member->join_date?->format('Y-m-d') ?? '';
    $this->status = $member->status;
    $this->user_id = $member->user_id;
    $this->branch_id = $member->branch_id;
  }

  public function switchToEditMode(): void
  {
    $this->modalMode = 'edit';
  }

  public function closeModal(): void
  {
    $this->showModal = false;
    $this->resetForm();
    $this->resetValidation();
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

  #[On('member-saved')]
  public function saveMember(): void
  {
    $this->validate();

    $data = [
      'full_name' => $this->full_name,
      'birth_date' => $this->birth_date,
      'gender' => $this->gender,
      'address' => $this->address ?: null,
      'email' => $this->email,
      'phone_number' => $this->phone_number ?: null,
      'join_date' => $this->join_date ?: null,
      'status' => $this->status,
      'user_id' => $this->user_id ?: null,
      'branch_id' => $this->branch_id ?: null,
    ];

    if ($this->editingId) {
      Member::findOrFail($this->editingId)->update($data);
      $message = 'Cập nhật member thành công!';
    } else {
      Member::create($data);
      $message = 'Thêm member mới thành công!';
    }

    $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
    $this->closeModal();
  }

  public function deleteMember(): void
  {
    if ($this->deletingId) {
      Member::findOrFail($this->deletingId)->delete();

      $this->dispatch('notify', [
        'type' => 'success',
        'message' => 'Xóa member thành công!'
      ]);

      $this->closeDeleteModal();
    }
  }

  public function clearFilters(): void
  {
    $this->search = '';
    $this->statusFilter = 'all';
    $this->branchFilter = null;
    $this->resetPage();
  }

  private function resetForm(): void
  {
    $this->editingId = null;
    $this->full_name = '';
    $this->birth_date = '';
    $this->gender = 0;
    $this->address = '';
    $this->email = '';
    $this->phone_number = '';
    $this->join_date = '';
    $this->status = 1;
    $this->user_id = null;
    $this->branch_id = null;
  }

  private function applySearch($query)
  {
    $search = "%{$this->search}%";

    return $query->where(function ($q) use ($search) {
      $q->where('full_name', 'like', $search)
        ->orWhere('email', 'like', $search)
        ->orWhere('phone_number', 'like', $search)
        ->orWhere('address', 'like', $search)
        ->orWhereHas('user', fn($u) => $u->where('student_code', 'like', $search))
        ->orWhereHas('branch', fn($b) => $b->where('branch_name', 'like', $search));
    });
  }

  public function render()
  {
    $members = Member::query()
      ->with(['branch', 'user'])
      ->when($this->search, fn($q) => $this->applySearch($q))
      ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
      ->when($this->branchFilter, fn($q) => $q->where('branch_id', $this->branchFilter))
      ->latest('created_at')
      ->paginate($this->perPage)
      ->withQueryString();

    return view('livewire.admin.manage-members', compact('members'));
  }
}