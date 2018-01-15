<?php

namespace App\Http\Controllers\Notification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function storeFcmToken(Request $request){
        try{
            $user = Auth::user();
            if($request->has('fcm_token') && $request->fcm_token != null){
                $user->update(['web_fcm_token' => $request->fcm_token]);
                $status = 200;
                $response = [
                    'message' => 'Token saved successfully.'
                ];
            }else{
                $status = 201;
                $response = [
                    'message' => 'Token not found'
                ];
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Store FCM token',
                'data' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $response = [
                'message' => 'Something went wrong'
            ];
        }
        return response()->json($response,$status);
    }
}
