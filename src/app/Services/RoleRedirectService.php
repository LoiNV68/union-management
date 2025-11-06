<?php

namespace App\Services;

use App\Models\User;

class RoleRedirectService
{
  /**
   * Get the redirect route based on user role.
   *
   * @param User $user
   * @return string
   */
  public function getRedirectRoute(User $user): string
  {
    $role = $user->role;

    // Role = 0: Union dashboard
    if ($role == 0) {
      return 'union.dashboard';
    }
    // Role = 1, 2: Admin dashboard
    elseif (in_array($role, [1, 2])) {
      return 'admin.dashboard';
    }

    // Default fallback
    return 'dashboard';
  }

  /**
   * Get the redirect URL based on user role.
   *
   * @param User $user
   * @return string
   */
  public function getRedirectUrl(User $user): string
  {
    $route = $this->getRedirectRoute($user);
    return route($route);
  }
}
