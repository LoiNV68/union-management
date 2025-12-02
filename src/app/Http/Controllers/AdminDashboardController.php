<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Activity;
use App\Models\Transaction;
use App\Models\MemberTransaction;
use App\Models\TrainingPoint;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
  /**
   * Display the admin dashboard.
   */
  public function index()
  {
    // Members statistics
    $totalMembers = Member::count();
    $activeMembers = Member::where('status', 1)->count();
    $inactiveMembers = Member::where('status', 0)->count();

    // Activities statistics
    $totalActivities = Activity::count();
    $upcomingActivities = Activity::where('start_date', '>', now())->count();
    $completedActivities = Activity::where('end_date', '<=', now())->count();
    $totalRegistrations = DB::table('activity_registration')->count();

    // Financial statistics
    $totalTransactions = Transaction::count();
    $activeTransactions = Transaction::where('status', 0)->count();
    $totalRevenue = Transaction::where('type', 0)->sum('amount');
    $totalExpense = Transaction::where('type', 1)->sum('amount');

    $paidTransactions = MemberTransaction::where('payment_status', 2)->count();
    $pendingTransactions = MemberTransaction::where('payment_status', 0)->count();
    $totalMemberTransactions = MemberTransaction::count();
    $paymentRate = $totalMemberTransactions > 0
      ? round(($paidTransactions / $totalMemberTransactions) * 100, 2)
      : 0;

    // Training points statistics
    $totalTrainingPoints = TrainingPoint::count();
    $avgTrainingPoint = TrainingPoint::avg('point') ?? 0;
    $currentSemester = Semester::orderByDesc('school_year')
      ->orderByDesc('semester')
      ->first();

    $currentSemesterAvg = 0;
    if ($currentSemester) {
      $currentSemesterAvg = TrainingPoint::where('semester_id', $currentSemester->id)
        ->avg('point') ?? 0;
    }

    // Recent activities
    $recentActivities = Activity::with('user')
      ->orderByDesc('created_at')
      ->limit(5)
      ->get();

    // Training points distribution
    $trainingPointsDistribution = [
      'excellent' => TrainingPoint::where('point', '>=', 90)->count(),
      'good' => TrainingPoint::whereBetween('point', [80, 89.99])->count(),
      'average' => TrainingPoint::whereBetween('point', [65, 79.99])->count(),
      'below' => TrainingPoint::where('point', '<', 65)->count(),
    ];

    return view('livewire.admin.dashboard', compact(
      'totalMembers',
      'activeMembers',
      'inactiveMembers',
      'totalActivities',
      'upcomingActivities',
      'completedActivities',
      'totalRegistrations',
      'totalTransactions',
      'activeTransactions',
      'totalRevenue',
      'totalExpense',
      'paidTransactions',
      'pendingTransactions',
      'paymentRate',
      'totalTrainingPoints',
      'avgTrainingPoint',
      'currentSemester',
      'currentSemesterAvg',
      'recentActivities',
      'trainingPointsDistribution'
    ));
  }
}