<?php

namespace App\Livewire\Admin;

use App\Models\TrainingPoint;
use App\Models\Member;
use App\Models\Semester;
use App\Events\TrainingPointUpdated;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTrainingPoints extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public int $perPage = 10;
    public bool $showCreateForm = false;
    public bool $showDeleteModal = false;
    public bool $showViewModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $filterSemesterId = null;
    public ?int $filterBranchId = null;

    // Form fields
    public ?int $member_id = null;
    public ?int $semester_id = null;
    public string $point = '';
    public string $note = '';
    public ?TrainingPoint $viewingTrainingPoint = null;

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

    public function updatedFilterSemesterId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterBranchId(): void
    {
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
        $trainingPoint = TrainingPoint::findOrFail($id);
        $this->editingId = $id;
        $this->member_id = $trainingPoint->member_id;
        $this->semester_id = $trainingPoint->semester_id;
        $this->point = $trainingPoint->point;
        $this->note = $trainingPoint->note ?? '';
        $this->showCreateForm = true;
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

    public function openViewModal(int $id): void
    {
        $this->viewingTrainingPoint = TrainingPoint::with(['member.user', 'semester', 'updater'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingTrainingPoint = null;
    }

    public function save(): void
    {
        $this->validate([
            'member_id' => 'required|exists:members,id',
            'semester_id' => 'required|exists:semesters,id',
            'point' => 'required|numeric|min:0|max:100',
            'note' => 'nullable|string|max:1000',
        ]);

        $data = [
            'member_id' => $this->member_id,
            'semester_id' => $this->semester_id,
            'point' => $this->point,
            'note' => $this->note,
            'updater_id' => Auth::id(),
        ];

        if ($this->editingId) {
            TrainingPoint::findOrFail($this->editingId)->update($data);
            $this->dispatch('training-point-updated');
            TrainingPointUpdated::dispatch(TrainingPoint::find($this->editingId));
        } else {
            // Check if record already exists
            $existing = TrainingPoint::where('member_id', $this->member_id)
                ->where('semester_id', $this->semester_id)
                ->first();

            if ($existing) {
                $this->addError('member_id', 'Điểm rèn luyện cho thành viên này trong học kỳ này đã tồn tại.');
                return;
            }

            $tp = TrainingPoint::create($data);
            $this->dispatch('training-point-created');
            TrainingPointUpdated::dispatch($tp);
        }

        $this->closeCreateForm();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $tp = TrainingPoint::findOrFail($this->deletingId);
            $tp->delete();
            $this->dispatch('training-point-deleted');
            TrainingPointUpdated::dispatch($tp);
            $this->closeDeleteModal();
        }
    }

    #[Computed]
    public function memberOptions()
    {
        return Member::with('user')
            ->where('status', 1)
            ->orderBy('full_name')
            ->get()
            ->map(fn($m) => [
                'value' => $m->id,
                'label' => $m->full_name . ' - ' . ($m->user?->student_code ?? '')
            ])->toArray();
    }

    #[Computed]
    public function semesterOptions()
    {
        return Semester::orderByDesc('school_year')
            ->orderByDesc('semester')
            ->get()
            ->map(fn($s) => [
                'value' => $s->id,
                'label' => $s->school_year . ' - Học kỳ ' . $s->semester
            ])->toArray();
    }

    #[Computed]
    public function branchOptions()
    {
        return \App\Models\Branch::orderBy('branch_name')
            ->get()
            ->map(fn($b) => [
                'value' => $b->id,
                'label' => $b->branch_name
            ])->toArray();
    }

    private function resetForm(): void
    {
        $this->member_id = null;
        $this->semester_id = null;
        $this->point = '';
        $this->note = '';
    }

    public function render()
    {
        return view('livewire.admin.manage-training-points', [
            'trainingPoints' => TrainingPoint::query()
                ->with(['member.user', 'semester', 'updater'])
                ->when($this->search, function ($query) {
                    $query->whereHas('member', function ($q) {
                        $q->where('full_name', 'like', '%' . $this->search . '%')
                            ->orWhereHas('user', function ($uq) {
                                $uq->where('student_code', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->filterSemesterId, function ($query) {
                    $query->where('semester_id', $this->filterSemesterId);
                })
                ->when($this->filterBranchId, function ($query) {
                    $query->whereHas('member', function ($q) {
                        $q->where('branch_id', $this->filterBranchId);
                    });
                })
                ->orderByDesc('updated_at')
                ->paginate($this->perPage)
                ->withQueryString(),
            'members' => [],
            'semesters' => [],
            'branches' => [],
        ]);
    }
    public function getListeners(): array
    {
        return [
            'echo:training-points,training-point.updated' => '$refresh',
            'training-point-created' => '$refresh',
            'training-point-updated' => '$refresh',
            'training-point-deleted' => '$refresh',
        ];
    }
}
