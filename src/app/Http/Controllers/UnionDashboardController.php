<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnionDashboardController extends Controller
{
  /**
   * Display the union dashboard.
   */
  public function index()
  {
    return view('livewire.union.dashboard');
  }
}