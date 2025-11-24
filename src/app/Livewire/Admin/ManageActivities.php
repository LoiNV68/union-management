<?php

namespace App\Livewire\Admin;

use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageActivities extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public int $perPage = 10;
    public ?int $editingId = null;
    public ?int $viewingId = null;
    public bool $showCreateForm = false;
    public bool $showDeleteModal = false;
    public bool $showNotificationModal = false;
    public bool $showRegistrationsModal = false;
    public ?int $deletingId = null;
    public ?int $notifyingActivityId = null;
    public ?int $registrationsActivityId = null;

    // Form fields
    public string $activity_name = '';
    public string $description = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $location = '';
    public string $type = '';
    public ?int $max_participants = null;

    // Notification fields
    public string $notification_title = '';
    public string $notification_content = '';

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()?->role, [1, 2]), 403);
    }

    // Listen to events from user component
    protected function getListeners(): array
    {
        return [
            'registration-created' => '$refresh',
            'registration-cancelled' => '$refresh',
            'echo:activities,RegistrationCreated' => '$refresh', // For WebSocket support later
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

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showCreateForm = true;
        $this->editingId = null;
        $this->viewingId = null;
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function openEditForm(int $id): void
    {
        $activity = Activity::findOrFail($id);
        $this->editingId = $id;
        $this->activity_name = $activity->activity_name;
        $this->description = $activity->description ?? '';
        $this->start_date = $activity->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $activity->end_date?->format('Y-m-d') ?? '';
        $this->location = $activity->location ?? '';
        $this->type = $activity->type;
        $this->max_participants = $activity->max_participants;
        $this->showCreateForm = true;
        $this->viewingId = null;
    }

    public function openViewModal(int $id): void
    {
        $this->viewingId = $id;
        $this->showCreateForm = false;
        $this->editingId = null;
    }

    public function closeViewModal(): void
    {
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

    public function closeNotificationModal(): void
    {
        $this->showNotificationModal = false;
        $this->notifyingActivityId = null;
        $this->notification_title = '';
        $this->notification_content = '';
    }

    public function openRegistrationsModal(int $activityId): void
    {
        $this->registrationsActivityId = $activityId;
        $this->showRegistrationsModal = true;
    }

    public function closeRegistrationsModal(): void
    {
        $this->showRegistrationsModal = false;
        $this->registrationsActivityId = null;
    }

    public function approveRegistration(int $registrationId): void
    {
        $registration = ActivityRegistration::findOrFail($registrationId);
        $activityId = $registration->activity_id;
        $registration->update([
            'registration_status' => 1, // Approved
        ]);
        $this->dispatch('registration-approved');
        $this->dispatch('activity-updated', activityId: $activityId)->to('union.register-activities');
    }

    public function rejectRegistration(int $registrationId): void
    {
        $registration = ActivityRegistration::findOrFail($registrationId);
        $activityId = $registration->activity_id;
        $registration->update([
            'registration_status' => 2, // Rejected
        ]);
        $this->dispatch('registration-rejected');
        $this->dispatch('activity-updated', activityId: $activityId)->to('union.register-activities');
    }

    public function saveActivity(): void
    {
        $rules = [
            'activity_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ];

        if ($this->editingId) {
            $rules['activity_name'][] = 'unique:activity,activity_name,' . $this->editingId;
        } else {
            $rules['activity_name'][] = 'unique:activity,activity_name';
        }

        $this->validate($rules);

        $data = [
            'activity_name' => $this->activity_name,
            'description' => $this->description ?: null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'location' => $this->location ?: null,
            'type' => $this->type,
            'max_participants' => $this->max_participants ?: null,
            'creator' => Auth::id(),
        ];

        if ($this->editingId) {
            Activity::findOrFail($this->editingId)->update($data);
            $this->dispatch('activity-updated');
        } else {
            Activity::create($data);
            $this->dispatch('activity-created');
            $this->dispatch('activity-list-updated')->to('union.register-activities');
        }

        $this->closeCreateForm();
    }

    public function deleteActivity(): void
    {
        if ($this->deletingId) {
            Activity::findOrFail($this->deletingId)->delete();
            $this->dispatch('activity-deleted');
            $this->closeDeleteModal();
        }
    }

    public function sendNotification(): void
    {
        $this->validate([
            'notification_title' => ['required', 'string', 'max:255'],
            'notification_content' => ['required', 'string'],
        ]);

        if (!$this->notifyingActivityId) {
            return;
        }

        $activity = Activity::findOrFail($this->notifyingActivityId);

        // Get all approved registered users for this activity
        $registeredUsers = $activity->registrations()
            ->where('registration_status', 1) // Only approved
            ->with('member.user')
            ->get()
            ->pluck('member.user.id')
            ->unique();

        // Send notification to each registered user
        foreach ($registeredUsers as $userId) {
            Notification::create([
                'title' => $this->notification_title,
                'content' => $this->notification_content,
                'date_sent' => now(),
                'sender_id' => Auth::id(),
                'receiver_id' => $userId,
                'notify_type' => 1, // Activity notification
            ]);
        }

        $this->dispatch('notification-sent');
        $this->closeNotificationModal();
    }

    private function resetForm(): void
    {
        $this->activity_name = '';
        $this->description = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->location = '';
        $this->type = '';
        $this->max_participants = null;
    }

    public function render()
    {
        return view('livewire.admin.manage-activities', [
            'activities' => Activity::query()
                ->with(['user', 'registrations'])
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
                ->orderByDesc('start_date')
                ->paginate($this->perPage)
                ->withQueryString(),
            'viewingActivity' => $this->viewingId ? Activity::with(['user', 'registrations.member'])->withCount('registrations')->find($this->viewingId) : null,
            'pendingRegistrations' => $this->registrationsActivityId ? ActivityRegistration::query()
                ->where('activity_id', $this->registrationsActivityId)
                ->where('registration_status', 0) // Pending
                ->with('member.user')
                ->get() : collect(),
            'approvedRegistrations' => $this->registrationsActivityId ? ActivityRegistration::query()
                ->where('activity_id', $this->registrationsActivityId)
                ->where('registration_status', 1) // Approved
                ->with('member.user')
                ->get() : collect(),
        ]);
    }
}
