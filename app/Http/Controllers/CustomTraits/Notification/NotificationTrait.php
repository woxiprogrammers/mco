<?php
/**
 * Created by Ameya Joshi.
 * Date: 8/1/18
 * Time: 12:32 PM
 */

namespace App\Http\Controllers\CustomTraits\Notification;

use Illuminate\Support\Facades\Log;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

trait NotificationTrait{
    public function sendPushNotification($title,$body,$tokens){
        try{
            Log::info('in send push notification');
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                ->setSound('default');
            $dataBuilder = new PayloadDataBuilder();
            /*$dataBuilder->addData(['a_data' => 'my_data']);*/
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();
            if(count($tokens) > 0){
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            }
            return true;
        }catch(\Exception $e){
            $data = [
                'action' => 'Send Push Notification',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return null;
        }
    }
}