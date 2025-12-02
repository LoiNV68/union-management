<?php

namespace App\Livewire\Admin;

use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageSemesters extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int $perPage = 10;
    public bool $showCreateForm = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $school_year = '';
    public int $semester = 1;

    public function mount(): void
    {
        abort_unless(in_array(Auth::user()?->role, [1, 2]), 403);
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
    }

    public function closeCreateForm(): void
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function openEditForm(int $id): void
    {
        $semester = Semester::findOrFail($id);
        $this->editingId = $id;
        $this->school_year = $semester->school_year;
        $this->semester = $semester->semester;
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
            'school_year' => 'required|string|max:20',
            'semester' => 'required|integer|min:1|max:3',
        ]);

        $data = [
            'school_year' => $this->school_year,
            'semester' => $this->semester,
        ];

        if ($this->editingId) {
            Semester::findOrFail($this->editingId)->update($data);
            $this->dispatch('semester-updated');
        } else {
            // Check if semester already exists
            $existing = Semester::where('school_year', $this->school_year)
                ->where('semester', $this->semester)
                ->first();

            if ($existing) {
                $this->addError('school_year', 'Học kỳ này đã tồn tại.');
                return;
            }

            Semester::create($data);
            $this->dispatch('semester-created');
        }

        $this->closeCreateForm();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            // Check if semester has training points
            $semester = Semester::withCount('trainingPoints')->find($this->deletingId);

            if ($semester && $semester->training_points_count > 0) {
                $this->addError('delete', 'Không thể xóa học kỳ đã có điểm rèn luyện.');
                return;
            }

            Semester::findOrFail($this->deletingId)->delete();
            $this->dispatch('semester-deleted');
            $this->closeDeleteModal();
        }
    }

    private function resetForm(): void
    {
        $this->school_year = '';
        $this->semester = 1;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.manage-semesters', [
            'semesters' => Semester::query()
                ->withCount('trainingPoints')
                ->orderByDesc('school_year')
                ->orderByDesc('semester')
                ->paginate($this->perPage)
                ->withQueryString(),
        ]);
    }
}
