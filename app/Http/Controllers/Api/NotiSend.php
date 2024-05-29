<?php

namespace App\Http\Controllers\Api;

class NotiSend
{

    static  function sendNotif($token, $receiver, $title, $msg)
    {

        $from = "AAAAayC1I6Y:APA91bFn-cnEk32ja33fDKPqzFCJaYj14PRF_-WemUbbqyGCxhzdeP_AXvhjvIUGx_4-ET08TZjUselT6mz2827yAkHlCdr12MVkgpsu8YZJh6bwE6Vp4K1BJg4nmycj6o7bGNeJAF48";
        $msg = array(
            'body'  => "$msg",
            'title' => "$title",
            'android_channel_id' => "realbazar",
            'receiver' => $receiver,
            'icon'  => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
            'sound' => 'mySound'/*Default sound*/
        );

        $fields = array(
            'to'        => $token,
            'notification'  => $msg,
            'data' => [
                'user_id' => "$receiver"
            ]
        );

        $headers = array(
            'Authorization: key=' . $from,
            'Content-Type: application/json'
        );
        //#Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
