<?php

namespace App\Livewire\Admin;

use App\Models\TrainingPoint;
use App\Models\Member;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
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
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $filterSemesterId = null;
    public ?int $filterBranchId = null;

    // Form fields
    public ?int $member_id = null;
    public ?int $semester_id = null;
    public string $point = '';

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

    public function save(): void
    {
        $this->validate([
            'member_id' => 'required|exists:members,id',
            'semester_id' => 'required|exists:semesters,id',
            'point' => 'required|numeric|min:0|max:100',
        ]);

        $data = [
            'member_id' => $this->member_id,
            'semester_id' => $this->semester_id,
            'point' => $this->point,
            'updater_id' => Auth::id(),
        ];

        if ($this->editingId) {
            TrainingPoint::findOrFail($this->editingId)->update($data);
            $this->dispatch('training-point-updated');
        } else {
            // Check if record already exists
            $existing = TrainingPoint::where('member_id', $this->member_id)
                ->where('semester_id', $this->semester_id)
                ->first();

            if ($existing) {
                $this->addError('member_id', 'Điểm rèn luyện cho thành viên này trong học kỳ này đã tồn tại.');
                return;
            }

            TrainingPoint::create($data);
            $this->dispatch('training-point-created');
        }

        $this->closeCreateForm();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            TrainingPoint::findOrFail($this->deletingId)->delete();
            $this->dispatch('training-point-deleted');
            $this->closeDeleteModal();
        }
    }

    private function resetForm(): void
    {
        $this->member_id = null;
        $this->semester_id = null;
        $this->point = '';
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
            'members' => Member::with('user')->where('status', 1)->orderBy('full_name')->get(),
            'semesters' => Semester::orderByDesc('school_year')->orderByDesc('semester')->get(),
            'branches' => \App\Models\Branch::orderBy('branch_name')->get(),
        ]);
    }
}
