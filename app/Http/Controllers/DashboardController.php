<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    public function redirectByRole(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user?->isVeterinaryStaff()) {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('client.dashboard');
    }

    public function admin(): View
    {
        $overview = $this->dashboardService->adminOverview();

        return view('dashboard.superadmin', $overview);
    }

    public function staff(Request $request): View
    {
        $overview = $this->dashboardService->staffOverview($request->user());

        return view('dashboard.admin', $overview);
    }

    public function client(Request $request): View
    {
        $overview = $this->dashboardService->clientOverview($request->user());

        return view('dashboard.client', $overview);
    }
}
