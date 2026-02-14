<?php

class LeaderboardService{
    public static function getFriendList(){
        $data["data"] = array(
            "leaderboardName" => "test",
            "friendList" => array(
                array(
                    "firstName" => "testBoi",
                    "value" => 100
                )
            )
        );

        return $data;
    }

    public static function getBatchFriendLists($playerObj, $request){
        $data["data"] = array();
        return $data;
    }
}
