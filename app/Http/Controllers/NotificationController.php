<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->with('relatedUser')
            ->paginate(20);
            
        return view('web.notifications', compact('notifications'));
    }
    
    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
    
    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
    
    /**
     * API: Get unread notification count
     */
    public function getUnreadCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }
    
    /**
     * API: Get latest notifications for dropdown
     */
    public function getLatest($limit = 10)
    {
        $notifications = auth()->user()->notifications()
            ->with('relatedUser:id,name,surname,avatar_url')
            ->take($limit)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'related_user' => $notification->relatedUser ? [
                        'name' => $notification->relatedUser->name . ' ' . $notification->relatedUser->surname,
                        'avatar' => $notification->relatedUser->avatar_url 
                            ? asset('storage/' . $notification->relatedUser->avatar_url) 
                            : null,
                        'initials' => $notification->relatedUser->initials
                    ] : null
                ];
            });
            
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ]);
    }
}
