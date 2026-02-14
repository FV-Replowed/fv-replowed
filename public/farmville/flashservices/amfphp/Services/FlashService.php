<?php 
require_once AMFPHP_ROOTPATH . "Helpers/player.php";
require_once AMFPHP_ROOTPATH . "Helpers/market_transactions.php";

require_once AMFPHP_ROOTPATH . "Functions/AvatarService.php";
require_once AMFPHP_ROOTPATH . "Functions/FarmQuestService.php";
require_once AMFPHP_ROOTPATH . "Functions/FBRequestService.php";
require_once AMFPHP_ROOTPATH . "Functions/FriendListService.php";
require_once AMFPHP_ROOTPATH . "Functions/FriendSetService.php";
require_once AMFPHP_ROOTPATH . "Functions/FVV10AnniversaryBirthdayCardService.php";
require_once AMFPHP_ROOTPATH . "Functions/LeaderboardService.php";
require_once AMFPHP_ROOTPATH . "Functions/LonelyAnimalFriendSetService.php";
require_once AMFPHP_ROOTPATH . "Functions/PresentService.php";
require_once AMFPHP_ROOTPATH . "Functions/SNPermissionsService.php";
require_once AMFPHP_ROOTPATH . "Functions/UserService.php";
require_once AMFPHP_ROOTPATH . "Functions/WatchToEarnRewardGrantService.php";
require_once AMFPHP_ROOTPATH . "Functions/WorldService.php";

class FlashService {
    
    public function dispatchBatch($userData, $reqData, $params3) {
        $data = array();

        $player = null;
        $market = null;
        $amf_debug = file_exists("/tmp/amf_debug");
        if ($amf_debug) {
            $count = is_array($reqData) ? count($reqData) : (is_object($reqData) ? count((array)$reqData) : 0);
            @file_put_contents("/tmp/amf_calls.log", "dispatchBatch start count={$count}\n", FILE_APPEND);
        }
        // Are we in init? if so, get masterId. If not, the id is in zy_user
        // We initialize the player object with our id.
        if (isset($userData->masterId) && $userData->masterId != ""){
            $player = new Player($userData->masterId);
            $market = new MarketTransactions($userData->masterId);
        }else{
            $player = new Player($userData->zy_user);
            $market = new MarketTransactions($userData->zy_user);
        }

        
        foreach ($reqData as $key => $requ){
            $data[$key] = array(
                "errorType" => 0,
                "errorData" => null,
                "sequenceNumber" => $requ->sequence,
                "worldTime" => time()
            );
            $data[$key]["metadata"] = array(
                "QuestComponent" => [
                    [
                        'name'      => "beatHauntedHallow-01-001",
                        'progress'  => [
                            0 => '0',  
                            1 => '0',  
                            2 => '0',  
                            3 => '0',  
                            4 => '0',  
                            5 => '0',  
                            6 => '0',  
                            7 => '0',  
                            8 => '0',  
                            9 => '0',  
                            10 => '0',  
                            11 => '0',  
                            12 => '0',  
                            13 => '0',  
                            14 => '0',  
                            
                        ],
                        'removed'   => false,
                        'expired'   => false,
                        'completed' => false
                    ]
                ]
             );

            try{
                $fn_details = explode(".", $requ->functionName);
                if ($amf_debug) {
                    @file_put_contents("/tmp/amf_calls.log", "AMF call: {$requ->functionName}\n", FILE_APPEND);
                }

                if (method_exists($fn_details[0], $fn_details[1])){
                    $data[$key] = array_merge($data[$key], call_user_func(array($fn_details[0], $fn_details[1]), $player, $requ, $market));
                } else {
                    if ($amf_debug) {
                        @file_put_contents("/tmp/amf_missing.log", "Missing AMF method: {$requ->functionName}\n", FILE_APPEND);
                    }
                }
            }catch (Exception $e){
                if ($amf_debug) {
                    $msg = "AMF exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
                    $msg .= $e->getTraceAsString() . "\n";
                    @file_put_contents("/tmp/amf_missing.log", $msg, FILE_APPEND);
                }
                // Do nothing
                //var_dump($e);
            }
            
            if ($requ->functionName == "FarmQuestService.questManagerStartReplayableQuestChain"){
                
            }

            
        } 

        return array(
            "errorType" => 0,
            "errorData" => null,
            "serverTime" => time(),
            "data" => $data
            
        );

    }

    
    
}

?>
