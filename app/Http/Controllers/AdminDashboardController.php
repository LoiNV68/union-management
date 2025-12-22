<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Activity;
use App\Models\Transaction;
use App\Models\MemberTransaction;
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

    // Explicitly using the model for cleaner counts if possible, else DB table
    $totalRegistrations = DB::table('activity_registration')->count();
    $approvedRegistrations = DB::table('activity_registration')->where('registration_status', 1)->count();
    $pendingRegistrations = DB::table('activity_registration')->where('registration_status', 0)->count();
    $avgParticipation = $totalActivities > 0 ? round($totalRegistrations / $totalActivities, 1) : 0;

    // Financial statistics
    $totalTransactions = Transaction::count();
    $activeTransactions = Transaction::where('status', 0)->count();


    $totalExpense = Transaction::where('type', 1)->sum('amount');

    // Calculate Actual Revenue (Collected)
    $actualRevenue = MemberTransaction::where('payment_status', 2)
      ->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
      ->where('transactions.type', 0)
      ->sum('transactions.amount');


    $pendingRevenue = MemberTransaction::where('payment_status', '!=', 2)
      ->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
      ->join('members', 'member_transactions.member_id', '=', 'members.id')
      ->where('transactions.type', 0)
      ->where('transactions.status', 0)
      ->where('members.status', 1)
      ->sum('transactions.amount');


    $totalPotentialRevenue = $actualRevenue + $pendingRevenue;
    $totalRevenue = $totalPotentialRevenue;

    $paidTransactions = MemberTransaction::where('payment_status', 2)->count();
    $pendingTransactions = MemberTransaction::where('payment_status', 0)->count();
    $totalMemberTransactions = MemberTransaction::count();
    $paymentRate = $totalMemberTransactions > 0
      ? round(($paidTransactions / $totalMemberTransactions) * 100, 2)
      : 0;

    // Recent activities
    $recentActivities = Activity::with('user')
      ->orderByDesc('created_at')
      ->limit(5)
      ->get();

    return view('livewire.admin.dashboard', compact(
      'totalMembers',
      'activeMembers',
      'inactiveMembers',
      'totalActivities',
      'upcomingActivities',
      'completedActivities',
      'totalRegistrations',
      'approvedRegistrations',
      'pendingRegistrations',
      'avgParticipation',
      'totalTransactions',
      'activeTransactions',
      'totalRevenue', // Keeping original var if needed by other parts, but chart will use specific ones
      'actualRevenue',
      'pendingRevenue',
      'totalPotentialRevenue',
      'totalExpense',
      'paidTransactions',
      'pendingTransactions',
      'paymentRate',
      'recentActivities'
    ));
  }
}