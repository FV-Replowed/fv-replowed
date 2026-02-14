<?php 

require_once AMFPHP_ROOTPATH . "Helpers/globals.php";
require_once AMFPHP_ROOTPATH . "Helpers/player.php";
require_once AMFPHP_ROOTPATH . "Helpers/market_transactions.php";

class UserService{
    function __construct()
    {
        
    }

    public static function initUser($playerObj, $request){
        $data["zySig"] = array(
            "zy_user" => $playerObj->getUid(),
            "zy_ts" => time(),
            "zy_session" => "thetestofthetime"
        );
        $data["data"] = $playerObj->getData($request);

        return $data;
    }

    public static function postInit(){
        $data["data"] = array(
            "postInitTimestampMetric" => time(),
            "friendsFertilized" => array(), // This is probably an array of plots
            "totalFriendsFertilized" => 0,
            "friendsFedAnimals" => array(), // This is an array of animals
            "totalFriendsFedAnimals" => 0,
            "showBookmark" => true,
            "showToolbarThankYou" => true,
            "toolbarGiftName" => true,
            "isAbleToPlayMusic" => true,
            "FOFData" => array(), //No clue. TODO
            "prereqDSData" => array(), // ^^
            "neighborCount" => 0,
            "fcSlotMachineRewards" => array(
                "allRewards" => array(

                ),
                "mgRewards" => array(

                )
            ),
            "hudIcons" => false,
            "crossGameGiftingState" => null,
            "avatarState" => array(
                "unlocked" => array(

                ),
                "configurations" => array(

                )
            ), // Need to add unlocked items here
            "breedingState" => null,
            "w2wState" => null,
            "bestSellers" => null,
            "completedQuests" => null,
            "completedReplayableQuests" => null,
            "pricingTests" => null,
            "buildingActions" => null,
            "lastPphActionType" => "PphAction",
            "communityGoalsData" => null,
            "turtleInnovationData" => array(),
            "dragonCollection" => null,
            "worldCurrencies" => array(),
            "lotteryData" => array(),
            "popupTwitterDialog" => false
        );

        return $data;
    }

    public static function resetSystemNotifications($player, $request){
        $data["data"] = array(
            "systemNotifications" => true,
            "dynamicSystemNotifications" => true
        );
        return $data;
    }

    public static function r2InterstitialPostInit($player, $request){
        $data["data"] = array(
            "showInterstitial" => false
        );
        return $data;
    }

    public static function incrementActionCount($player, $request){
        $data["data"] = true;
        return $data;
    }

    public static function updateFeatureFrequencyWithBackoff($player, $request){
        $data["data"] = true;
        return $data;
    }

    public static function updateFeatureFrequencyTimestamp($player, $request){
        $data["data"] = true;
        return $data;
    }

    public static function resetActionCount($player, $request){
        $data["data"] = true;
        return $data;
    }

    public static function setItemFlag($player, $request){
        $data["data"] = true;
        return $data;
    }

    public static function getBalance(){
        $data["data"] = array(
            "gold" => 100000,
            "cash" => 101000
        );

        return $data;
    }

    public static function getMOTD(){
        $data["data"] = array(
            "motdData" => array(
                "name" => "PAOK",
            )
        );

        return $data;
    }

    public static function setSeenFlag($player, $request){
        global $db;
        // Let's get our current seenFlags
        $query = "SELECT seenFlags FROM usermeta WHERE uid = '". $player->getUid(). "'";

        $conn = $db->getDb();

        $result = $conn->query($query);

        $res = $result->fetch_assoc();

        // Unserialize it
        $flags = unserialize($res['seenFlags']);

        // Extract the actual flag from the request
        $toAdd = $request->params[0];

        // Add the next one
        $flags[$toAdd] = true;
        
        $query = "UPDATE usermeta SET `seenFlags` = '" . serialize($flags) . "' WHERE uid = '". $player->getUid(). "'";

        $conn->query($query);

        return [];
    }
}
