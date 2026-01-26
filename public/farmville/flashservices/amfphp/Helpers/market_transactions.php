<?php 
require_once AMFPHP_ROOTPATH . "Helpers/globals.php";
require_once AMFPHP_ROOTPATH . "Helpers/database.php";
require_once AMFPHP_ROOTPATH . "Helpers/user_resources.php";
require_once AMFPHP_ROOTPATH . "Helpers/general_functions.php";

// TODO: adjust structure
class MarketTransactions {
    private $uid = null;
    private $db = null;

    public function __construct($pid) {
        $this->uid = $pid;
        $this->db = new Database();
    }

    public function newTransaction(string $type, object $data){
        switch ($type){
            case "sell":
                $this->sellItem($data);
                break;
            case "harvest":
                $this->harvestCrop($data);
                break;
            case "place":
                $this->buyItem($data);
                break;
            case "plow":
                $this->plowLand();
                break;
        }
    }

    // TODO: improve accuracy
    public function sellItem(object $data){
        $res = getItemByName($data->itemName, "db");
        
        if ($res){
            $saleValue = (int) ($res["cost"] ?? 0);
            $saleValue = (int) ($saleValue * 0.05);
            return UserResources::adjustGold($this->uid, $saleValue);
        }

        return false;
    }

    public function harvestCrop(object $data){        
        $res = getItemByName($data->itemName, "db");
        
        if ($res){
            $coinYield = (int) ($res["coinYield"] ?? 0);
            return UserResources::adjustGold($this->uid, $coinYield);
        }
        
        return false;
    }

    // TODO: add cash support
    public function buyItem(object $data){
        $res = getItemByName($data->itemName, "db");

        if ($res){
            $cost = (int) ($res["cost"] ?? 0);
            $plantXp = (int) ($res["plantXp"] ?? 0);
            $result1 = UserResources::adjustGold($this->uid, -$cost);
            $result2 = userResources::addXp($this->uid, $plantXp);
            return ($result1 && $result2);
        }

        return false;
    }

    public function plowLand(){
        $cost = 15;
        $plowXp = 1;
        $result1 = UserResources::adjustGold($this->uid, -$cost);
        $result2 = UserResources::addXp($this->uid, $plowXp);

        return ($result1 && $result2);
    }
}
