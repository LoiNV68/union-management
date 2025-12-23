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

    // Activities statistics
    $totalActivities = Activity::count();
    $upcomingActivities = Activity::where('start_date', '>', now())->count();
    $completedActivities = Activity::where('end_date', '<=', now())->count();

    // Activity registration stats
    $totalRegistrations = DB::table('activity_registration')->count();
    $approvedRegistrations = DB::table('activity_registration')->where('registration_status', 1)->count();
    $pendingRegistrations = DB::table('activity_registration')->where('registration_status', 0)->count();
    $avgParticipation = $totalActivities > 0 ? round($totalRegistrations / $totalActivities, 1) : 0;

    return view('livewire.admin.dashboard', compact(
      'totalMembers',
      'activeMembers',
      'totalActivities',
      'upcomingActivities',
      'completedActivities',
      'totalRegistrations',
      'approvedRegistrations',
      'pendingRegistrations',
      'avgParticipation'
    ));
  }
}