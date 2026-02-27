<?php

class WatchToEarnRewardGrantService{
    public static function generateDailyTokens($playerObj, $request){
        $data["data"] = array(
            "tokens" => 0
        );
        return $data;
    }

    public static function getUserZid($playerObj, $request){
        $zid = (string) $playerObj->getUid();
        return array(
            "success" => true,
            "zid" => $zid,
            "data" => array(
                "zid" => $zid
            )
        );
    }
}
