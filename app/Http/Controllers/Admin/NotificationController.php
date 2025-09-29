<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        // Check if it's an AJAX request (for sidebar notifications)
        if (request()->expectsJson()) {
            $notifications = AdminNotification::orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'body' => $notification->body,
                        'is_read' => $notification->is_read,
                        'created_at' => $notification->created_at->format('M d, Y h:i A'),
                    ];
                });

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => AdminNotification::where('is_read', false)->count()
            ]);
        }

        // For regular page view
        $notifications = AdminNotification::orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    public function markAsRead()
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markSingleAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function clear()
    {
        AdminNotification::truncate();
        return response()->json(['success' => true]);
    }
}
