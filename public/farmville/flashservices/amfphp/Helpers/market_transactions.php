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
            return UserResources::addGold($this->uid, $saleValue);
        }

        return false;
    }

    public function harvestCrop(object $data){        
        $res = getItemByName($data->itemName, "db");
        
        if ($res){
            $coinYield = (int) ($res["coinYield"] ?? 0);
            return UserResources::addGold($this->uid, $coinYield);
        }
        
        return false;
    }

    // TODO: add cash support
    public function buyItem(object $data){
        $res = getItemByName($data->itemName, "db");

        if ($res){
            $cost = (int) ($res["cost"] ?? 0);
            $plantXp = (int) ($res["plantXp"] ?? 0);
            $result1 = UserResources::removeGold($this->uid, $cost);
            $result2 = userResources::addXp($this->uid, $plantXp);
            $result = ($result1 && $result2);

            if ($result && ($res["type"] ?? "") === "change_farm" && ($res["subtype"] ?? "") === "expand_farm") {
                $newSize = 0;
                if (isset($res["squares"])) {
                    $newSize = (int) $res["squares"];
                } else if (isset($res["size"])) {
                    $newSize = ((int) $res["size"] * 4) + 2;
                }

                if ($newSize > 0) {
                    $currentWorldType = get_meta($this->uid, "currentWorldType") ?: "farm";
                    $conn = $this->db->getDb();

                    $stmt = $conn->prepare("SELECT sizeX FROM userworlds WHERE uid = ? AND type = ?");
                    $stmt->bind_param("ss", $this->uid, $currentWorldType);
                    $stmt->execute();
                    $resultRow = $stmt->get_result()->fetch_assoc();
                    $currentSize = $resultRow ? (int) $resultRow["sizeX"] : 0;

                    if ($newSize > $currentSize) {
                        $stmt = $conn->prepare("UPDATE userworlds SET sizeX = ?, sizeY = ? WHERE uid = ? AND type = ?");
                        $stmt->bind_param("iiss", $newSize, $newSize, $this->uid, $currentWorldType);
                        $stmt->execute();
                    }

                    $this->db->destroy();
                }
            }

            return $result;
        }

        return false;
    }

    public function plowLand(){
        $cost = 15;
        $plowXp = 1;
        $result1 = UserResources::removeGold($this->uid, $cost);
        $result2 = UserResources::addXp($this->uid, $plowXp);

        return ($result1 && $result2);
    }
}
