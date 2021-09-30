<?php

namespace App\Traits;


trait SMS
{


    public static function sendsms($sms_to, $sms_msg){

        $user       = 'API7TJSL2GLSL';
        $pass       = 'API7TJSL2GLSL281ZD';
        $sms_from   = 'AZURAMART';
        // $sms_to = '601114416494';
        // $sms_msg = 'RM0.00 UKSHOP: Order #ODID-1178 has dispatched, Track:  EK123456789MY';
        $mode = env('APP_MODE');
        if($mode == 'PRODUCTION'){
            if ($sms_to && $sms_msg) {
                $query_string = "api.aspx?apiusername=".$user."&apipassword=".$pass;
                $query_string .= "&senderid=".rawurlencode($sms_from)."&mobileno=".rawurlencode($sms_to);
                $query_string .= "&message=".rawurlencode(stripslashes($sms_msg)) . "&languagetype=1";
                $url = "http://gateway.onewaysms.com.au:10001/".$query_string;
                $fd = @implode ('', file ($url));
                if ($fd){
                    if ($fd > 0) {
                        Print("MT ID : " . $fd);
                        $ok = "success";
                    }else {
                        print("Please refer to API on Error : " . $fd);
                        $ok = "fail";
                    }
                }else{
                        // no contact with gateway
                    $ok = "fail";
                }
            }else{
                $ok = "fail";
            }

        }else{
            $ok = "success";
        }

        return $ok;
    }

}
