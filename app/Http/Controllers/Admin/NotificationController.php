<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    public function markAsRead()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
