<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Jobs\ProcessNotification;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        try{
            $data = $request->validate([
                'user_id'=>'required',
                'type' => 'required|string',
                'title' => 'nullable|string',
                'message' => 'required|string',
                'payload' => 'nullable|array',
            ]);
            $check_user=User::find($data['user_id']);
            if(!$check_user){
                return response()->json(['error'=>'User not found']);
            }
            $notification = Notification::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'title' => $data['title'] ?? null,
                'message' => $data['message'],
                'payload' => $data['payload'] ?? null,
                'status' => 'pending',
            ]);
            
    
            ProcessNotification::dispatch($notification);
            return response()->json($notification, 201);
        }catch(\Exception $e){
            return response()->json(['error'=>$e]);
        }

    }

    public function recent()
    {
        return Cache::remember('recent_notifications', 30, function () {
            return Notification::latest()->limit(20)->get();
        });
    }

    public function getAllusers(){
        $users = User::role(['admin'])->get();
        return response()->json($users, 200);
    }

    public function summary()
    {
        return Cache::remember('notification_summary', 30, function () {
            return [
                'sent' => Notification::where('status', 'sent')->count(),
                'failed' => Notification::where('status', 'failed')->count(),
                'pending' => Notification::where('status', 'pending')->count(),
            ];
        });
    }
}
