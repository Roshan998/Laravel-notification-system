<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $summary = Cache::remember('notification_summary', 30, function () {
            return [
                'sent' => Notification::where('status', 'sent')->count(),
                'failed' => Notification::where('status', 'failed')->count(),
                'pending' => Notification::where('status', 'pending')->count(),
            ];
        });

        $recent = Notification::with('user')->latest()->paginate(8);

        return view('admin.dashboard', compact('summary', 'recent'));
    }
}
