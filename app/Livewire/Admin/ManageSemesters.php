<?php

namespace App\Livewire\Admin;

use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SemestersExport;

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
    public ?int $start_year = null;
    public ?int $end_year = null;
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

    public function updatedStartYear($value): void
    {
        if ($value) {
            $this->end_year = (int) $value + 1;
        }
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->start_year = (int) date('Y');
        $this->end_year = $this->start_year + 1;
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

        $years = explode('-', $this->school_year);
        if (count($years) === 2) {
            $this->start_year = (int) $years[0];
            $this->end_year = (int) $years[1];
        } else {
            $this->start_year = (int) $this->school_year;
            $this->end_year = $this->start_year + 1;
        }

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
        $currentYear = (int) date('Y');

        $this->validate([
            'start_year' => ['required', 'integer', 'min:' . $currentYear, 'max:2100'],
            'end_year' => ['required', 'integer'],
            'semester' => ['required', 'integer', 'min:1', 'max:3'],
        ], [
            'start_year.required' => 'Vui lòng nhập năm bắt đầu.',
            'start_year.min' => 'Năm bắt đầu không được là năm trong quá khứ.',
            'end_year.required' => 'Vui lòng nhập năm kết thúc.',
        ]);

        if ($this->end_year !== $this->start_year + 1) {
            $this->addError('end_year', 'Năm kết thúc phải bằng năm bắt đầu cộng 1.');
            return;
        }

        $this->school_year = $this->start_year . '-' . $this->end_year;

        $data = [
            'school_year' => $this->school_year,
            'semester' => $this->semester,
        ];

        if ($this->editingId) {
            // Check for duplicate but exclude current
            $existing = Semester::where('school_year', $this->school_year)
                ->where('semester', $this->semester)
                ->where('id', '<>', $this->editingId)
                ->first();

            if ($existing) {
                $this->addError('start_year', 'Học kỳ cho năm học này đã tồn tại.');
                return;
            }

            Semester::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Đã cập nhật học kỳ.'
            ]);
        } else {
            // Check if semester already exists
            $existing = Semester::where('school_year', $this->school_year)
                ->where('semester', $this->semester)
                ->first();

            if ($existing) {
                $this->addError('start_year', 'Học kỳ cho năm học này đã tồn tại.');
                return;
            }

            Semester::create($data);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Đã thêm học kỳ mới.'
            ]);
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
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Đã xóa học kỳ.'
            ]);
            $this->closeDeleteModal();
        }
    }

    public function exportExcel()
    {
        return Excel::download(new SemestersExport, 'semesters.xlsx');
    }

    #[Computed]
    public function semesterOptions()
    {
        return [
            ['value' => 1, 'label' => '📚 Học kỳ 1'],
            ['value' => 2, 'label' => '📖 Học kỳ 2'],
            ['value' => 3, 'label' => '☀️ Học kỳ 3 (Hè)'],
        ];
    }

    #[Computed]
    public function yearOptions()
    {
        $currentYear = (int) date('Y');
        $years = [];
        for ($i = $currentYear; $i <= $currentYear + 50; $i++) {
            $years[] = [
                'value' => $i,
                'label' => (string) $i
            ];
        }
        return $years;
    }

    private function resetForm(): void
    {
        $this->school_year = '';
        $this->start_year = null;
        $this->end_year = null;
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
