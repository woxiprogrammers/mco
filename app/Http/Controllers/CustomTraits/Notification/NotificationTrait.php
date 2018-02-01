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
    public function sendPushNotification($title,$body,$webTokens,$mobileTokens,$tag = ''){
        try{
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);
            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                ->setTag($tag)
                ->setSound('default');
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'title' => $title,
                'body' => $body,
                'tag' => $tag
            ]);
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();
            if(count($webTokens) > 0){
                $webDownstreamResponse = FCM::sendTo($webTokens, $option, $notification, $data);
            }
            if(count($mobileTokens) > 0){
                $mobileDownstreamResponse = FCM::sendTo($mobileTokens, $option, null, $data);
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