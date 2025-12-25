<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->get(); // Get all notifications, newest first

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);

        // Mark notification as read
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
