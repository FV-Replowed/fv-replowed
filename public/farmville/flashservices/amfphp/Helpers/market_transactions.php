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

        if ($res && is_numeric($this->uid)){
            $coinYield = (int) ($res["coinYield"] ?? 0);

            $conn = $db->getDb();
            $stmt = $conn->prepare("UPDATE usermeta SET gold = gold + ? WHERE uid = ?");
            $stmt->bind_param("is", $coinYield, $this->uid);
            $stmt->execute();
            $db->destroy();
        }
    }

    public function buyItem(object $data){
        global $db;
        
        $res = getItemByName($data->itemName, "db");
        $conn = $db->getDb();
        if ($res && $res["cost"]){
            //Calc xp gain
            $buyXp = 0;
            if ($res['plantXp']){
                $buyXp = $res['plantXp'];
            }else{
                $buyXp = $res['cost'] * 0.01;
            }
            $query = "UPDATE usermeta SET `gold` = gold - " . $res["cost"] . ", xp = xp + ".$buyXp." WHERE uid = '". $this->uid. "'";
            $conn->query($query);
        }elseif ($res && $res["cash"]){
            $buyXp = $res['cash'] * 100;
            $query = "UPDATE usermeta SET `gold` = gold - " . $res["cost"] . ", xp = xp + ".$buyXp." WHERE uid = '". $this->uid. "'";
            $conn->query($query);
        }
        $db->destroy();
    }

    public function plowLand(){
        global $db;
        $conn = $db->getDb();
        $query = "UPDATE usermeta SET `gold` = gold - 15, `xp` = xp + 1 WHERE uid = '". $this->uid. "'";
        $conn->query($query);
        $db->destroy();
    }
}


