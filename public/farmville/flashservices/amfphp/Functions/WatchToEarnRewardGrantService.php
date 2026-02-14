<?php

class WatchToEarnRewardGrantService{
    public static function generateDailyTokens($playerObj, $request){
        $data["data"] = array(
            "tokens" => 0
        );
        return $data;
    }

    public static function getUserZid($playerObj, $request){
        $data["data"] = $playerObj->getUid();
        return $data;
    }
}

