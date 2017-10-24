<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    //
    protected $table = "bots";

    public static function createNew($ip,$port){
        $newBot = new Bot();
        $newBot->setIP($ip);
        $newBot->setPort($port);
        return $newBot;
    }

    public function setIP($ip){
        $this->ip = $ip;
    }
    public function setPort($port){
        $this->port = $port;
    }
}
