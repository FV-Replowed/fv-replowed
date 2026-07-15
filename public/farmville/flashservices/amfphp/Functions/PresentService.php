<?php

class PresentService{
    public static function receiveAllPresents($playerObj, $request){
        $data["data"] = array(
            "presents" => array()
        );
        return $data;
    }
}

