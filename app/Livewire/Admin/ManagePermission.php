<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePermission extends Component
{
  use WithPagination;

  public string $new_student_code = '';
  public string $new_password = '';
  public int $new_role = 0;
  public string $search = '';
  public int $perPage = 10;

  // Modal state
  public bool $showToggleLockModal = false;
  public bool $showDeleteModal = false;
  public ?int $selectedUserId = null;
  public bool $selectedUserLocked = false;

  private function ensureSuperAdmin(): void
  {
    abort_unless(Auth::user()?->role === 2, 403);
  }

  public function mount(): void
  {
    $this->ensureSuperAdmin();
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

  public function createUser(): void
  {
    $this->validate([
      'new_student_code' => ['required', 'string', 'max:255', 'unique:users,student_code'],
      'new_password' => ['required', 'string', 'min:6'],
      'new_role' => ['required', 'integer', 'in:0,1,2'],
    ]);

    User::query()->create([
      'student_code' => $this->new_student_code,
      'password' => Hash::make($this->new_password),
      'role' => $this->new_role,
    ]);

    $this->reset(['new_student_code', 'new_password', 'new_role']);
    $this->dispatch('user-created');
  }

  public function setRole(int $userId, $role): void
  {
    $this->ensureSuperAdmin();
    $roleValue = (int) $role;
    if (!in_array($roleValue, [0, 1, 2], true)) {
      return;
    }

    $user = User::query()->findOrFail($userId);
    $user->update(['role' => $roleValue]);
    $this->dispatch('user-updated');
  }

  // Toggle Lock Modal Methods
  public function openToggleLockModal(int $userId, bool $isLocked): void
  {
    $this->selectedUserId = $userId;
    $this->selectedUserLocked = $isLocked;
    $this->showToggleLockModal = true;
  }

  public function closeToggleLockModal(): void
  {
    $this->showToggleLockModal = false;
    $this->selectedUserId = null;
    $this->selectedUserLocked = false;
  }

  public function confirmToggleLock(): void
  {
    if ($this->selectedUserId === null) {
      return;
    }

    $this->ensureSuperAdmin();
    $user = User::query()->findOrFail($this->selectedUserId);
    $user->update(['is_locked' => !$this->selectedUserLocked]);
    $this->dispatch('user-updated');
    $this->closeToggleLockModal();
  }

  // Delete Modal Methods
  public function openDeleteModal(int $userId): void
  {
    $this->selectedUserId = $userId;
    $this->showDeleteModal = true;
  }

  public function closeDeleteModal(): void
  {
    $this->showDeleteModal = false;
    $this->selectedUserId = null;
    $this->resetErrorBag('delete');
  }

  public function confirmDelete(): void
  {
    if ($this->selectedUserId === null) {
      return;
    }

    try {
      $this->ensureSuperAdmin();
      $user = User::query()->findOrFail($this->selectedUserId);
      $user->delete();
      $this->resetPage();
      $this->dispatch('user-updated');
      $this->closeDeleteModal();
    } catch (\Exception $e) {
      $this->addError('delete', __('Có lỗi xảy ra khi xóa người dùng.'));
    }
  }

  #[Computed]
  public function roleOptions()
  {
    return [
      ['value' => 0, 'label' => '👤 User'],
      ['value' => 1, 'label' => '🛡️ Admin'],
      ['value' => 2, 'label' => '👑 Super Admin'],
    ];
  }

  public function render()
  {
    $query = User::query()
      ->select('id', 'student_code', 'role', 'is_locked')
      ->where('id', '<>', Auth::id())
      ->when($this->search, fn($q) => $q->where('student_code', 'like', "%{$this->search}%"))
      ->orderByDesc('id');

    $users = $query->paginate($this->perPage)->withQueryString();

    return view('livewire.admin.manage-permission', [
      'users' => $users,
    ]);
  }
}
