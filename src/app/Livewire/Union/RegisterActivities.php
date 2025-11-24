<?php

namespace App\Livewire\Union;

use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\Member;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RegisterActivities extends Component
{
  use WithPagination;

  protected $paginationTheme = 'tailwind';

  public string $search = '';
  public int $perPage = 10;
  public ?int $viewingActivityId = null;
  public bool $showActivityModal = false;
  public bool $showNotificationsPanel = false;

  public function mount(): void
  {
    abort_unless(Auth::user()?->role === 0, 403);
  }

  // Listen to events from admin component
  protected function getListeners(): array
  {
    return [
      'activity-updated' => '$refresh',
      'activity-list-updated' => '$refresh',
      'echo:activities,ActivityUpdated' => '$refresh', // For WebSocket support later
    ];
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

  public function openActivityModal(int $activityId): void
  {
    $this->viewingActivityId = $activityId;
    $this->showActivityModal = true;
  }

  public function closeActivityModal(): void
  {
    $this->showActivityModal = false;
    $this->viewingActivityId = null;
  }

  public function toggleNotificationsPanel(): void
  {
    $this->showNotificationsPanel = !$this->showNotificationsPanel;

    if ($this->showNotificationsPanel) {
      // Mark notifications as read when viewing
      Notification::query()
        ->where('receiver_id', Auth::id())
        ->whereNull('read_at')
        ->update(['read_at' => now()]);
    }
  }

  public function registerActivity(int $activityId): void
  {
    $user = Auth::user();
    $member = Member::where('user_id', $user->id)->first();

    if (!$member) {
      $this->addError('register', 'Bạn không phải là thành viên. Vui lòng liên hệ quản trị viên.');
      return;
    }

    // Check if already registered (including pending)
    $existing = ActivityRegistration::query()
      ->where('member_id', $member->id)
      ->where('activity_id', $activityId)
      ->whereIn('registration_status', [0, 1]) // Pending or Approved
      ->first();

    if ($existing) {
      $this->addError('register', 'Bạn đã đăng ký hoạt động này rồi.');
      return;
    }

    // Check max participants (only count approved)
    $activity = Activity::findOrFail($activityId);
    if ($activity->max_participants) {
      $approvedCount = $activity->registrations()
        ->where('registration_status', 1)
        ->count();
      if ($approvedCount >= $activity->max_participants) {
        $this->addError('register', 'Hoạt động này đã đủ số lượng tham gia.');
        return;
      }
    }

    // Create registration with pending status
    ActivityRegistration::create([
      'member_id' => $member->id,
      'activity_id' => $activityId,
      'registration_time' => now()->toDateString(),
      'registration_status' => 0, // Pending approval
      'note' => null,
    ]);

    $this->dispatch('activity-registered');
    $this->dispatch('registration-created')->to('admin.manage-activities');
    $this->closeActivityModal();
  }
  public function cancelRegistration(int $registrationId): void
  {
    $registration = ActivityRegistration::findOrFail($registrationId);

    // Check if belongs to current user
    if ($registration->member->user_id !== Auth::id()) {
      $this->addError('cancel', 'Không có quyền hủy đăng ký này.');
      return;
    }

    $registration->delete();
    $this->dispatch('activity-cancelled');
    $this->dispatch('registration-cancelled')->to('admin.manage-activities');
  }


  public function render()
  {
    $user = Auth::user();
    $member = Member::where('user_id', $user->id)->first();

    return view('livewire.union.register-activities', [
      'activities' => Activity::query()
        ->withCount('registrations')
        ->withCount([
          'registrations as approved_registrations_count' => function ($query) {
            $query->where('registration_status', 1);
          }
        ])
        ->when($this->search, function ($query) {
          $query->where('activity_name', 'like', '%' . $this->search . '%')
            ->orWhere('location', 'like', '%' . $this->search . '%');
        })
        ->where('end_date', '>=', now()->toDateString())
        ->orderBy('start_date')
        ->paginate($this->perPage)
        ->withQueryString(),
      'registeredActivities' => $member ? ActivityRegistration::query()
        ->where('member_id', $member->id)
        ->with('activity')
        ->latest()
        ->get() : collect(),
      'viewingActivity' => $this->viewingActivityId ? Activity::with('user')->withCount('registrations')->find($this->viewingActivityId) : null,

    ]);
  }
}
