<?php

namespace App\Http\Controllers;

use App\Bot;
use Request;
class NewBotController extends Controller{
    public function hi()
    {
        $msg = [];
        $request = Request::all();
        if( !isset($request['ip'])){
            $msg["reply"] = "ip is missing";
            return response()->json($msg);
        }

        if( !isset($request['port'])){
            $msg["reply"] = "port is missing";
            return response()->json($msg);
        }
        $ip = trim($request['ip']);
        $valid = filter_var($ip, FILTER_VALIDATE_IP);
        if(!$valid){
            $msg["reply"] = "invalid ip";
            return response()->json($msg);
        }
        $port = trim($request['port']);
        if(is_numeric($port)){
            $port = (int) $port;
        }
        else{
            $msg["reply"] = "invalid port (not a number)";
            return response()->json($msg);
        }
        if($port < 0 || $port > 65535){
            $msg["reply"] = "invalid port (out if range)";
            return response()->json($msg);
        }
        $bot = Bot::createNew($ip,$port);
        $bot->save();

        $msg["reply"] = "thank you";
        return response()->json($msg);
    }
}