<?php 
require_once AMFPHP_ROOTPATH . "Helpers/globals.php";
require_once AMFPHP_ROOTPATH . "Helpers/database.php";
require_once AMFPHP_ROOTPATH . "Helpers/general_functions.php";

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
            case "place":
                $this->buyItem($data);
                break;
            case "plow":
                $this->plowLand();
                break;
        }
    }

    public function sellItem(object $data){
        global $db;

        $res = getItemByName($data->itemName, "db");

        if ($res && $res["cost"]){
            $conn = $db->getDb();
            $sellCost = $res['cost'] * 0.05;
            $query = "UPDATE usermeta SET `gold` = gold + " . $sellCost . " WHERE uid = '". $this->uid. "'";
            $conn->query($query);
            $db->destroy();
        }
    }

    public function harvestCrop(object $data){
        global $db;
        
        $res = getItemByName($data->itemName, "db");
        $maxGold = 999_999_999; // specified by the engine

        if ($res && is_numeric($this->uid)){
            $coinYield = (int) ($res["coinYield"] ?? 0);

            $conn = $db->getDb();
            $stmt = $conn->prepare("UPDATE usermeta SET gold = LEAST(gold + ?, ?) WHERE uid = ?");
            $stmt->bind_param("iis", $coinYield, $maxGold, $this->uid);
            $stmt->execute();
            $db->destroy();
        }
    }

    // TODO: add cash support
    public function buyItem(object $data){
        global $db;
        
        $res = getItemByName($data->itemName, "db");
        $maxXp = 2_147_400_000; // minimum points required to reach the highest level

        if ($res && is_numeric($this->uid)){
            $cost = (int) ($res["cost"] ?? 0);
            $plantXp = (int) ($res["plantXp"] ?? 0);

            $conn = $db->getDb();
            $stmt = $conn->prepare("UPDATE usermeta SET gold = GREATEST(gold - ?, 0), xp = LEAST(xp + ?, ?) WHERE uid = ?");
            $stmt->bind_param("iiis", $cost, $plantXp, $maxXp, $this->uid);
            $stmt->execute();
            $db->destroy();
        }
    }

    public function plowLand(){
        global $db;

        $cost = 15;
        $maxXp = 2_147_400_000; // minimum points required to reach the highest level
        $plowXp = 1;

        if (is_numeric($this->uid)){
            $conn = $db->getDb();
            $stmt = $conn->prepare("UPDATE usermeta SET gold = GREATEST(gold - ?, 0), xp = LEAST(xp + ?, ?) WHERE uid = ?");
            $stmt->bind_param("iiis", $cost, $plowXp, $maxXp, $this->uid);
            $stmt->execute();
            $db->destroy();
        }
    }
}


