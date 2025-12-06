<?php

namespace App\Livewire\Union;

use App\Models\TrainingPoint;
use App\Models\Member;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ViewTrainingPoints extends Component
{
    public ?int $filterSemesterId = null;

    public function mount(): void
    {
        abort_unless(Auth::user()?->role === 0, 403);
    }

    public function updatedFilterSemesterId(): void
    {
        // Trigger re-render when semester filter changes
    }

    public function getListeners(): array
    {
        return [
            'echo:training-points,training-point.updated' => '$refresh',
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            abort(403, 'Bạn không phải là thành viên.');
        }

        $trainingPointsQuery = TrainingPoint::query()
            ->with(['semester', 'updater'])
            ->where('member_id', $member->id)
            ->when($this->filterSemesterId, function ($query) {
                $query->where('semester_id', $this->filterSemesterId);
            })
            ->orderByDesc('updated_at');

        $trainingPoints = $trainingPointsQuery->get();

        // Calculate statistics
        $totalPoints = $trainingPoints->sum('point');
        $averagePoints = $trainingPoints->count() > 0 ? $totalPoints / $trainingPoints->count() : 0;

        return view('livewire.union.view-training-points', [
            'trainingPoints' => $trainingPoints,
            'semesters' => Semester::orderByDesc('school_year')->orderByDesc('semester')->get(),
            'totalPoints' => $totalPoints,
            'averagePoints' => $averagePoints,
            'member' => $member,
        ]);
    }
}
