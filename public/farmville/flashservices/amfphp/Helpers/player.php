<?php 
require_once AMFPHP_ROOTPATH . "Helpers/database.php";
require_once AMFPHP_ROOTPATH . "Helpers/general_functions.php";

class Player {

    private $uid = null;
    private $pData = array();
    private $worldData = array();
    private $avatarData = array();
    private $db = null;

    public function __construct($id) {
        $this->uid = $id;
        $this->db = new Database();
    }

    public function getUid(){
        return $this->uid;
    }

    // TODO: make quotes consistent
    public function getData($requ) {
        // either the database will have the expected data, or it won't
        // not going to bother validating further
        $conn = $this->db->getDb();
        $stmt = $conn->prepare("SELECT * FROM usermeta WHERE uid = ?");
        $stmt->bind_param("s", $this->uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->db->destroy();

        $currentWorldType = get_meta($this->uid, "currentWorldType") ? get_meta($this->uid, "currentWorldType") : "farm";
        $currentWorld = getWorldByType($this->uid, $currentWorldType);

        $this->pData = array(
            "sequenceNumber" => $requ->sequence,
            "sequenceId" => 1483867184,
            
            "crossPromos" => null,
            "flashHotParams" => array(
                "STAT_SAMPLE_ZLOC_FAIL" => 10,
                "ZYNGA_USER_ID" => $this->uid,
                "ZRUNTIME_KEY_HIDE_STATS_HUD" => false,
                "SKIP_NEW_CMS_MODULES" => false,
                "BINGO" => '{"CADENCENAME": "bingo","START_DATE": "05/13/2013","END_DATE": "05/30/2013","PREVIOUS_END_DATE": "05/30/2013","TITLE": "FARM BINGO","WINDOW_BACKGROUND": "assets/dialogs/FV_Support/FV_Bingo/Bingo_bg_default.png","MOTD": "assets/dialogs/FV_motd_Bingo.swf","BUY_RANDOM_PRICE": 2,"BUY_SPECIFIC_PRICE": 5,"COOLDOWN_HOURS": 6,"AUTOPOP_HOURS": 10,"PRIZES": "saddleshoetree,atthehop,sheep_thickglasses,cow_designersuit,pegacorn_poodleskirt","CARD_NUMBERS": "14,8,2,5,11,25,17,30,26,19,44,42,37,39,57,53,48,60,58,63,74,72,66,61","CARD_NUMBERS_NOT_SELECTED": "1,3,4,6,7,9,10,12,13,15,16,18,20,21,22,23,24,27,28,29,31,32,33,34,35,36,38,40,41,43,45,46,47,49,50,51,52,54,55,56,59,62,64,65,67,68,69,70,71,73,75"}',
                "REALITEMNAME_ENABLED" => true,
                "MARKET_REPOP_BLACKLIST" => ""
            ),
            "wishData" => array(
                "wishSenders" => null,
                "wishRewardLink" => null,
                "wishName" => null,
                "wishImage" => null
            ),
            "energy" => $row['energy'],
            "locale" => "en_US",
            "witherOn" => true,
            "isFarmvilleFan" => false,
            "fanPageStatuses" => array(),
            "subscriptionStatus" => "",
            "promos" => array(),
            "socialActions" => null,
            "snExtendedPermissions" => [
                "publish_actions",
                "user_games_activity",
                "friends_games_activity",
                "publish_actions",
                "user_birthday",
                "read_stream",
                "user_friends",
                "extended_permissions_gift_given"
            ],
            "systemNotifications" => true,
            "dynamicSystemNotifications" => true,
            "hasValidUnwitherClock" => 1,
            "errorPopupEnabled" => 1,
            "suppressDialogs" => false,
            "qaPopupBlock" => false,
            "neighbors" => compressArray($this->getCurrentNeighbors()),
            "npcs" => array(),
            "pendingPresents" => array(),
            "bumperCropPaid" => 0,
            "firstDay" => false,
            "friendUnwithered" => 0,
            "geoip" => null,
            "purchaseHistory" => array(),
            "experiments" => [],
            "userLocale" => "en_US",
            "req_initUserStartTimestamp" => time(),
            "world" => $currentWorld,
            "craftingState" => array(
                "craftingItems" => array(
                    array(
                        "itemCode" => "SB",
                        "quantity" => 2,
                        "price" => null
                    ),
                    array(
                        "itemCode" => "SB",
                        "quantity" => 2,
                        "price" => null
                    )
                ),
                "nextCalendarDate" => 12,
                "calendarDate" => 11,
                "maxCapacity" => 5,
                "currentMarketStallCount" => 1,
                "firstCraft" => "stall",
                "shoppingState" => null,
                "pendingRewards" => null,
                "craftingSkillState" => array(
                    "recipeStates" => array()
                )

            ),
            "userInfo" => array(
                "currentWorldType" => $currentWorldType,
                "attr" => array(
                    "name" => $row["firstName"]
                ),
                "unlockedWorldTypes" => array(
                    //"yuletide",
                    //"pumpkin",
                    //"aloha",
                    //"amsterdam",
                    "madagascar", // GOOD
                    //"farmfest",
                    //"borabora",
                    "canada", // GOOD
                    "santavillage", // GOOD
                    //"spooky",
                    //"ireland",
                    //"cocoland",
                    "alaska", // GOOD
                    //"twenties",
                    //"southindia",
                    //"casablanca",
                    "winternord", // GOOD
                    //"halloweenmad",
                    //"israel",
                    //"newfrontier",
                    //"russia",
                    "dragonvalley", // GOOD
                    "caribbean", // GOOD
                    //"whitewinter",
                    "brazil", // GOOD
                    //"turtleisland",
                    "farm", // GOOD
                    "england", // GOOD
                    "fisherman", // GOOD
                    "winterwonderland", // GOOD
                    "hawaii", // GOOD
                    "asia", // GOOD
                    //"angler", // VERY BAD
                    "atlantis", // GOOD
                    "australia", // GOOD
                    "space", // GOOD
                    "candy", // GOOD
                    "fforest", // GOOD
                    "hlights", // GOOD
                    "rainforest", // GOOD
                    "oz", // GOOD
                    "mediterranean", // GOOD
                    "oasis", // GOOD
                    "storybook", // GOOD
                    //"avalon",
                    "wildwest", // GOOD
                    "treasuretides", // GOOD
                    "africa", // GOOD
                    "transylvania", // GOOD
                    //"winter",
                    //"india",
                    //"jungle",
                    "garden", // GOOD (but interesting)
                    "village", // GOOD
                    "hallow", // GOOD
                    "htown", // GOOD
                    "sleepyhollow", // GOOD
                    "toyland", // GOOD
                    "meadows", // GOOD
                    "glen", // GOOD
                    //"japan", // interesting
                    //"mount",
                    "limbo", // GOOD
                    //"xmas",
                    //"midwest",
                    //"underwater",
                    //"dreamworld",
                    //"anglofrench",
                    "halloweenusa", // GOOD
                    //"tuscany",
                ),
                "player" => array(
                    "gold" => $row['gold'],
                    "cash" => $row['cash'],
                    "xp" => $row['xp'],
                    "energyMax" => $row['energyMax'],
                    "energy" => $row['energy'],
                    "options" => array(
                        "sfxDisabled" => false,
                        "musicDisabled" => false,
                        "animationDisabled" => false
                    ),
                    "storageData" => [
                        "-6" => array()
                        ],
                    "hasVisitFriend" => false,
                    "achievements" => array(),
                    "achCounters" => null,
                    "mastery" => [array(
                        "An" => 1,
                        "SB" => 1,
                        "EP" => 1,
                        "WH" => 1,
                        "SY" => 1,
                        "Tn" => 1,
                        "SQ" => 1,
                        "Tl" => 1,
                        "PU" => 1,
                        "SK" => 1,
                        "AR" => 1,
                        "RC" => 1,
                        "RB" => 1,
                        "00" => 1,
                        "CO" => 1,
                        "CI" => 1,
                        "YB" => 1,
                        "PP" => 1,
                        "0D" => 1,
                        "AE" => 1,
                        "PN" => 1,
                        "01" => 1,
                        "SM" => 1,
                        "BB" => 1,
                        "WT" => 1,
                        "G2" => 1,
                        "TO" => 1,
                        "02" => 1,
                        "PO" => 1,
                        "CA" => 1,
                        "CF" => 1,
                        "CN" => 1,
                        "SF" => 1,
                        "GC" => 1,
                        "CE" => 1,
                        "GT" => 1,
                        "DL" => 1,
                        "B9" => 1,
                        "RW" => 1,
                        "03" => 1,
                        "SU" => 1,
                        "PZ" => 1,
                        "YW" => 1,
                        "ON" => 1,
                        "BI" => 1,
                        "04" => 1,
                        "SR" => 1,
                        "AS" => 1,
                        "Un" => 1,
                        "Tm" => 1,
                    )],
                    "masteryCounters" => [array( // Make me go up
                        "An" => 1,
                        "SB" => 1,
                        "EP" => 1,
                        "WH" => 1,
                        "SY" => 1,
                        "Tn" => 1,
                        "SQ" => 1,
                        "Tl" => 1,
                        "PU" => 1,
                        "SK" => 1,
                        "AR" => 1,
                        "RC" => 1,
                        "RB" => 1,
                        "00" => 1,
                        "CO" => 1,
                        "CI" => 1,
                        "YB" => 1,
                        "PP" => 1,
                        "0D" => 1,
                        "AE" => 1,
                        "PN" => 1,
                        "01" => 1,
                        "SM" => 1,
                        "BB" => 1,
                        "WT" => 1,
                        "G2" => 1,
                        "TO" => 1,
                        "02" => 1,
                        "PO" => 1,
                        "CA" => 1,
                        "CF" => 1,
                        "CN" => 1,
                        "SF" => 1,
                        "GC" => 1,
                        "CE" => 1,
                        "GT" => 1,
                        "DL" => 1,
                        "B9" => 1,
                        "RW" => 1,
                        "03" => 1,
                        "SU" => 1,
                        "PZ" => 1,
                        "YW" => 1,
                        "ON" => 1,
                        "BI" => 1,
                        "04" => 1,
                        "SR" => 1,
                        "AS" => 1,
                        "Un" => 1,
                        "Tm" => 1,
                    )],
                    'organicCounters' => null,
                    'organicCertificationTotal' => null,
                    'collectionCounters' => null,
                    'storedCollections' => null,
                    'collectionLevels' => null,
                    'hasUnlimitedLights' => null,
                    'farmServiceCredits' => [],
                    'altGraphicCredits' => null,
                    'numLightsLeft' => 0,
                    'numOpenedPresents' => 0,
                    'dateOfLastPublishPermissionRequest' => 0,
                    'hasPublishPermission' => true,
                    'lastHorseStableSendTime' => 0,
                    'lastFrenchChateauSendTime' => 0,
                    'lastNurserySendTime' => 0,
                    'incrementalGateArray' => 0,
                    'progressBarData' => null,
                    'neighborPlumbingAddExcludeList' => null,
                    'pendingNeighbors' => $this->getPendingNeighbors(),
                    'neighbors' => $this->getCurrentNeighborUids(),
                    'lastSocialPlumbingActionTime' => 0,
                    'adoptedAnimals' => 0,
                    'superCropsStatus' => null,
                    'lotteryTickets' => 0,
                    'lonelyAnimalCode' => 0,
                    // 'currentMOTD' => array( //this is a dialog type. No idea what that means
                    //     "name" => "eimai xontros"
                    // ),
                    'motdSeenFlags' => 0,
                    'limitedSaleExpirations' => 0,
                    'cashPurchasedTotal' => "100000",
                    'initialCashPurchaseTransactions' => 0,
                    'initialCPATransactions' => 0,
                    'avatarSurfacingEnabled' => false,
                    'avatarSurfacingFrequency' => 0,
                    'avatarSurfacingItem' => null,
                    'transactionLog' => null,
                    'farmTalkPermission' => true,
                    'chatLastMessageReadTime' => 0,
                    // 'storageCapacityHash' => array(
                    //     "-2" => 500
                    // ),
                    'userId' => $row['uid'],
                    'featureCredits' => "10",
                    'incrementalFriendChecks' => array(),
                    'friendRewards' => null,
                    'seenFlags' => unserialize($row['seenFlags']), //tutorial flag
                    'itemFlags' => array("giftcard" => ""),
                    'featureFrequency' => array(
                        "AvatarIndicatorLastInteraction" => 10,
                        "r2AddNeighborInFlashPop" => 0

                    ),
                    'externalLevels' => array(
                        
                    ),
                    'actionCounts' => ["AvatarSurfaceThrottle_backoff_base"],
                    'neighborActionLimits' => false,
                    'energyManager' => array(
                        "turboChargers" => 0
                    ),
                    "isAKeynoteUser" => "1"
                ),
                "worldSummaryData" => array(
                    $currentWorldType => array(
                        "firstLoaded" => strtotime($currentWorld['creation']),
                        "lastLoaded" => strtotime(date("Y-m-d h:i:s"))
                    )
                ), //TODO
                "is_new" => $row["isNew"],
                "firstDay" => $row["firstDay"],
                "firstDayTimestamp" => 0,
                "featureOptions"=> array(
                    "world_seasons" => array(
                        "farm" => 0,
                        "avalon" => 1
                    )
                ),
                "iconCodes" => [
                    "scratchCard"
                ],
                "avatar" => $this->getAvatar()
                
            )
        );

        return $this->pData;
    }

    

    public function getAvatar(){
        $this->avatarData = null;

        if (is_numeric($this->uid)){
            $conn = $this->db->getDb();
            $stmt = $conn->prepare("SELECT value FROM useravatars WHERE uid = ?");
            $stmt->bind_param("s", $this->uid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc() ?? [];
            $this->avatarData = unserialize($row["value"] ?? null) ?? null;
            $this->db->destroy();
        }

        return $this->avatarData;
    }

    /*
    private function plow($newObj, $currObjects){
        // Does it already exist? (Maybe planted thing that we harvested and now we plow again)
        $exists = "";

        foreach ($currObjects as $key => $tile){
            if ($newObj["id"] == $tile["id"]){
                $exists = $key;
            }
        }

        if ($exists != ""){
            unset($currObjects[$exists]);
        }

        $currObjects[] = $newObj;

        return $currObjects;

    }

    private function plant($newObj, $currObjects){
        // Does it already exist? (Maybe planted thing that we harvested and now we plow again)
        $exists = "";

        foreach ($currObjects as $key => $tile){
            if ($newObj["id"] == $tile["id"]){
                $exists = $key;
            }
        }

        if ($exists != ""){
            unset($currObjects[$exists]);
            $currObjects[] = $newObj;
        }

        

        return $currObjects;

    }
    */

    public function setWorld($newObj, $action, $newSizeX = null, $newSizeY = null){

        $currentWorldType = get_meta($this->uid, "currentWorldType") ? get_meta($this->uid, "currentWorldType") : "farm";

        if (empty($this->worldData)){
            $currWorld = getWorldByType($this->uid, $currentWorldType);
        }else{
            $currWorld = $this->worldData;
        }
        $delActions = ['sell', 'clear'];
        
        
        // Does it already exist? (Maybe planted thing that we harvested and now we plow again)
        $exists = "";
        $maxId = 0;
        $newId = 0;

        foreach ($currWorld["objectsArray"] as $key => $tile){
            if ($newObj->id == $tile->id){
                $exists = $key;
            }

            if ($tile->id > $maxId){
                $maxId = $tile->id;
            }
        }

        // If we plow or place non-existing items, the new object's id will be a new id, stored in the db and returned to the game
        // Set exists to "" because the item is new
        if (($action == "plow" || $action == "place") && $newObj->id >= 63000){
            $newId = $maxId + 1;
            $newObj->id = $newId;
            $exists = "";
        }
        // print_r($newObj);
        
        if ($exists != "" && !in_array($action, $delActions)){
            $currWorld["objectsArray"][$exists] = $newObj;
            
        }else if (in_array($action, $delActions)){
            unset($currWorld["objectsArray"][$exists]);
            $currWorld["objectsArray"] = array_values($currWorld["objectsArray"]);
            
        }else {
            $currWorld["objectsArray"][] = $newObj;
            
        }
        
        // Did size change?
        if ($newSizeX != null){
            $currWorld["sizeX"] = $newSizeX;
        }
        
        if ($newSizeY != null){
            $currWorld["sizeY"] = $newSizeY;
        }
        
        $this->worldData = $currWorld;
        $objects = serialize($currWorld["objectsArray"]);
        
        // No need for further validation (Would have already failed)
        $conn = $this->db->getDb();
        $stmt = $conn->prepare("UPDATE userworlds SET objects = ?, sizeX = ?, sizeY = ? WHERE uid = ?");
        $stmt->bind_param("siis", $objects, $currWorld["sizeX"], $currWorld["sizeY"], $this->uid);        
        $stmt->execute();
        $this->db->destroy();
    
        if ($newId > 0){
            return $newId;
        }
        
        return 0;
    }

    public function setAvatar($attribs){
        if (is_numeric($this->uid) && is_array($attribs)){
            $attribs = serialize($attribs);
            $conn = $this->db->getDb();
            $stmt = $conn->prepare("UPDATE useravatars SET value = ? WHERE uid = ?");
            $stmt->bind_param("ss", $attribs, $this->uid);
            $stmt->execute();
            $this->db->destroy();
        }
    }

    public function getPlayerDataForNeighbor(){
        $rows = [];

        if (is_numeric($this->uid)){
            $conn = $this->db->getDb();
            $stmt = $conn->prepare("SELECT us.uid as uid, us.name as name, um.firstName as firstname, um.lastName as lastname FROM users AS us INNER JOIN usermeta AS um ON us.uid = um.uid WHERE us.uid <> ?");
            $stmt->bind_param("s", $this->uid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
            }
            
            $this->db->destroy();
        }

        return $rows;
    }

    public function getCurrentNeighbors(){

        $neighborData = [];
        $currNeighbors = get_meta($this->uid, 'current_neighbors');

        if ($currNeighbors){
            $currNeighborUids = unserialize($currNeighbors);

            foreach ($currNeighborUids as $neighbor) {
                $neighborData[] = $this->getPlayerData($neighbor);
            }
        }
        return !empty($neighborData) ? $neighborData : [];
    }

    private function getCurrentNeighborUids(){
        $currNeighbors = get_meta($this->uid, 'current_neighbors');

        if (!$currNeighbors){
            return [];
        }
        return unserialize($currNeighbors);
    }

    public function setPendingNeighbors($pid){

        $res_uns = [];

        $currNeighbors = get_meta($pid, 'pending_neighbors');
        if ($currNeighbors){
            $res_uns = unserialize($currNeighbors);
            if (!in_array($this->uid, $res_uns)){
                $res_uns[] = $this->uid;
            }
        }else{
            $res_uns[] = $this->uid;
        }
        
        set_meta($pid, 'pending_neighbors', serialize($res_uns));

    }

    public function getPendingNeighbors(){
        $pendingNeighbors = get_meta($this->uid, 'pending_neighbors');

        if (!$pendingNeighbors){
            return [];
        }
        return unserialize($pendingNeighbors);
    }

    public function getPlayerData($uid){
        if (is_numeric($uid)){
            $conn = $this->db->getDb();
            $stmt = $conn->prepare("SELECT us.uid as uid, us.name as name, um.firstName as firstname, um.lastName as lastname FROM users AS us INNER JOIN usermeta AS um ON us.uid = um.uid WHERE us.uid = ?");
            $stmt->bind_param("s", $uid);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $this->db->destroy();
            
            // if the row's contents are invalid, loading SHOULD fail
            return (object) [
                "uid" => $row['uid'],
                "name" => $row['name'],
                "first_name" => $row['firstname'],
                "last_name" => $row['lastname'],
                "is_app_user" => true,
                "valid" => true,
                "allowed_restrictions" => false,
                "pic_square" => "",
                "pic_big" => ""
            ];
        }

        return (object) [];
    }
}