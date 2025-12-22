<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

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
  public bool $showPasswordModal = false;
  public ?int $selectedUserId = null;
  public bool $selectedUserLocked = false;
  public string $new_password_update = '';

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
      'new_student_code' => ['required', 'string', 'regex:/^225[0-9]{7}$/', 'unique:users,student_code'],
      'new_password' => ['required', 'string', 'min:6', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.@$!%*?&\/|\\\]).+$/'],
      'new_role' => ['required', 'integer', 'in:0,1,2'],
    ], [
      'new_student_code.required' => 'Vui lòng nhập mã sinh viên.',
      'new_student_code.regex' => 'Mã sinh viên phải có định dạng 225xxxxxxx (10 chữ số, bắt đầu bằng 225).',
      'new_student_code.unique' => 'Mã sinh viên đã tồn tại trong hệ thống.',
      'new_password.required' => 'Vui lòng nhập mật khẩu.',
      'new_password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
      'new_password.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
      'new_password.regex' => 'Mật khẩu phải có ít nhất 1 chữ thường, 1 chữ in hoa, 1 số và 1 ký tự đặc biệt (@$!%*?&. / | \).',
      'new_role.required' => 'Vui lòng chọn vai trò.',
      'new_role.integer' => 'Vai trò phải hợp lệ.',
      'new_role.in' => 'Vai trò không hợp lệ.',
    ]);

    User::query()->create([
      'student_code' => $this->new_student_code,
      'password' => Hash::make($this->new_password),
      'role' => $this->new_role,
    ]);

    $this->reset(['new_student_code', 'new_password', 'new_role']);
    $this->dispatch('notify', [
      'type' => 'success',
      'message' => 'Đã tạo tài khoản mới.'
    ]);
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
    $this->dispatch('notify', [
      'type' => 'success',
      'message' => 'Đã cập nhật vai trò người dùng.'
    ]);
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
    $this->dispatch('notify', [
      'type' => 'success',
      'message' => 'Đã cập nhật trạng thái khóa người dùng.'
    ]);
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
      $this->dispatch('notify', [
        'type' => 'success',
        'message' => 'Đã xóa người dùng.'
      ]);
      $this->closeDeleteModal();
    } catch (\Exception $e) {
      $this->addError('delete', __('Có lỗi xảy ra khi xóa người dùng.'));
    }
  }

  // Password Modal Methods
  public function openPasswordModal(int $userId): void
  {
    $this->selectedUserId = $userId;
    $this->new_password_update = '';
    $this->showPasswordModal = true;
  }

  public function closePasswordModal(): void
  {
    $this->showPasswordModal = false;
    $this->selectedUserId = null;
    $this->new_password_update = '';
    $this->resetErrorBag('new_password_update');
  }

  public function confirmPasswordUpdate(): void
  {
    if ($this->selectedUserId === null) {
      return;
    }

    $this->validate([
      'new_password_update' => ['required', 'string', 'min:6', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.@$!%*?&\/|\\\]).+$/'],
    ], [
      'new_password_update.required' => 'Vui lòng nhập mật khẩu mới.',
      'new_password_update.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
      'new_password_update.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
      'new_password_update.regex' => 'Mật khẩu phải có ít nhất 1 chữ thường, 1 chữ in hoa, 1 số và 1 ký tự đặc biệt (@$!%*?&. / | \).',
    ]);

    try {
      $this->ensureSuperAdmin();
      $user = User::query()->findOrFail($this->selectedUserId);
      $user->update([
        'password' => Hash::make($this->new_password_update)
      ]);

      $this->dispatch('notify', [
        'type' => 'success',
        'message' => 'Đã đặt lại mật khẩu thành công.'
      ]);
      $this->closePasswordModal();
    } catch (\Exception $e) {
      $this->addError('new_password_update', __('Có lỗi xảy ra khi đổi mật khẩu.'));
    }
  }

  public function exportExcel()
  {
    $this->ensureSuperAdmin();
    return Excel::download(new UsersExport, 'users.xlsx');
  }

  #[Computed]
  public function roleOptions()
  {
    return [
      ['value' => 0, 'label' => '👤 Đoàn viên'],
      ['value' => 1, 'label' => '🛡️ Cán bộ đoàn'],
      ['value' => 2, 'label' => '👑 Quản trị viên'],
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
