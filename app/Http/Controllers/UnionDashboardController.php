<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ActivityRegistration;
use App\Models\MemberTransaction;
use Illuminate\Support\Facades\Auth;

class UnionDashboardController extends Controller
{
  /**
   * Display the union dashboard.
   */
  public function index()
  {
    $user = Auth::user();
    $member = Member::where('user_id', $user->id)->first();

    if (!$member) {
      return view('livewire.union.dashboard', [
        'error' => 'Bạn chưa được đăng ký là thành viên.'
      ]);
    }

    // Activity statistics
    $totalRegistrations = ActivityRegistration::where('member_id', $member->id)->count();
    $approvedRegistrations = ActivityRegistration::where('member_id', $member->id)
      ->where('registration_status', 1)
      ->count();
    $pendingRegistrations = ActivityRegistration::where('member_id', $member->id)
      ->where('registration_status', 0)
      ->count();

    $upcomingRegistrations = ActivityRegistration::where('member_id', $member->id)
      ->where('registration_status', 1)
      ->whereHas('activity', function ($q) {
        $q->where('start_date', '>', now());
      })->count();

    $completedRegistrations = ActivityRegistration::where('member_id', $member->id)
      ->where('registration_status', 1)
      ->whereHas('activity', function ($q) {
        $q->where('start_date', '<=', now());
      })->count();

    // Financial statistics
    $totalTransactions = MemberTransaction::where('member_id', $member->id)->count();
    $paidTransactions = MemberTransaction::where('member_id', $member->id)
      ->where('payment_status', 2)
      ->count();
    $pendingPayments = MemberTransaction::where('member_id', $member->id)
      ->where('payment_status', 0)
      ->count();

    $totalAmount = MemberTransaction::where('member_id', $member->id)
      ->with('transaction')
      ->get()
      ->sum(function ($mt) {
        return $mt->transaction->amount ?? 0;
      });

    $paidAmount = MemberTransaction::where('member_id', $member->id)
      ->where('payment_status', 2)
      ->with('transaction')
      ->get()
      ->sum(function ($mt) {
        return $mt->transaction->amount ?? 0;
      });

    // Recent activity registrations
    $recentRegistrations = ActivityRegistration::where('member_id', $member->id)
      ->with('activity')
      ->orderByDesc('created_at')
      ->limit(5)
      ->get();

    return view('livewire.union.dashboard', compact(
      'member',
      'totalRegistrations',
      'approvedRegistrations',
      'pendingRegistrations',
      'upcomingRegistrations',
      'completedRegistrations',
      'totalTransactions',
      'paidTransactions',
      'pendingPayments',
      'totalAmount',
      'paidAmount',
      'recentRegistrations'
    ));
  }
}