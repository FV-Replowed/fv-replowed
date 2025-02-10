<?php
    require_once AMFPHP_ROOTPATH . "Helpers/globals.php";
    /**
     * Get User Metadata
     * 
     * @param string $uid User ID
     * @param string $meta_key Meta Key
     * @return string The value of the meta field
     *                False for incalid $uid of non found $meta_key
     */
    function get_meta($uid, $meta_key){
        global $db;

        $meta = null;

        $conn = $db->getDb();
        $query = "SELECT meta_value FROM playermeta WHERE meta_key = '{$meta_key}' AND uid = '{$uid}'";
        // var_dump($conn->query($query));
        $result = $conn->query($query);
        if ($result->num_rows > 0){
            $meta = $result->fetch_assoc();
        }
        $db->destroy();
        return $meta != null ? $meta['meta_value'] : false;
    }


    /**
     * Set User Metadata
     * 
     * @param string $uid User ID
     * @param string $meta_key Meta Key
     * @param string $meta_value Meta Value to update or insert
     * 
     * @return string The value of the meta field
     *                False for incalid $uid of non found $meta_key
     */
    function set_meta($uid, $meta_key, $meta_value){
        global $db;

        $meta_rec = get_meta($uid, $meta_key);

        $conn = $db->getDb();
        $query = "";
        if ($meta_rec){
            $query = "UPDATE playermeta SET `meta_value` = '{$meta_value}' WHERE uid = '{$uid}' AND `meta_key` = '{$meta_key}'";
        }else{
            $query = "INSERT INTO `playermeta` (`uid`, `meta_key`, `meta_value`) VALUES ('{$uid}','{$meta_key}','{$meta_value}')";
        }

        $conn->query($query);

        $db->destroy();
        
    }

    function compressArray($array){

        // Convert the array to JSON (compatible with ActionScript)
        $jsonData = json_encode($array);

        // Compress the JSON string
        $compressedData = gzcompress($jsonData);

        // Encode to Base64
        $base64Encoded = base64_encode($compressedData);

        return $base64Encoded;
    }


    function getItemByName($itemName, $method = "json"){
        global $db;

        if ($method == "db"){
            $query = "SELECT * FROM items WHERE name = '{$itemName}'";
            $conn = $db->getDb();
            $result = $conn->query($query);
            $res = $result->fetch_assoc();
            $db->destroy();
            return unserialize($res['data']);
        }else{
            $items_str = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/props/items.json");
            $items = json_decode($items_str);
            foreach ($items->settings->items->item as $item){
                if ($item->name == $itemName){
                    
                    return (array) $item;
                }
            }
        }

        return false;
    }


    function getWorldByUid($uid){
        global $db;

        $worldData = [];

        $query = "SELECT * FROM userworlds WHERE type = 'farm' AND uid = '{$uid}'";
        $conn = $db->getDb();
        $result = $conn->query($query);

        
        $db->destroy();
        
        if ($result->num_rows > 0){
            $res = $result->fetch_assoc();
            $worldData["type"] = $res["type"];
            $worldData["sizeX"] = $res["sizeX"];
            $worldData["sizeY"] = $res["sizeY"];
            $worldData["objectsArray"] = unserialize($res["objects"]);
            $worldData['creation'] = $res["created_at"];
            $worldData["messageManager"] = array();
        }

        return $worldData;
    }

    function getWorldByType($uid, $type = "farm"){
        global $db;

        $worldData = [];

        $query = "SELECT * FROM userworlds WHERE type = '{$type}' AND uid = '{$uid}'";
        $conn = $db->getDb();
        $result = $conn->query($query);

        
        $db->destroy();
        
        if ($result->num_rows > 0){
            $res = $result->fetch_assoc();
            $worldData["type"] = $res["type"];
            $worldData["sizeX"] = $res["sizeX"];
            $worldData["sizeY"] = $res["sizeY"];
            $worldData["objectsArray"] = unserialize($res["objects"]);
            $worldData['creation'] = $res["created_at"];
            $worldData["messageManager"] = array();
        }else{
            $worldData = createWorldByType($uid, $type);
        }

       

        
        return $worldData;
    }

    function createWorldByType($uid, $type = "farm" ){
        global $db;

        $newWorld = serialize(array(
                (object)[
                    "id" => 1,
                    "state" => "grown",
                    "isBigPlot"=> false,
                    "plantTime" => (time() * 1000) - 14450, //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 19,
                        "y" => 9,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => "strawberry",
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 2,
                    "state" => "grown",
                    "isBigPlot"=> false,
                    "plantTime" => (time() * 1000) - 14450, //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 19,
                        "y" => 13,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => "strawberry",
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 3,
                    "state" => "plowed",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 23,
                        "y" => 13,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 4,
                    "state" => "plowed",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 23,
                        "y" => 9,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 5,
                    "state" => "fallow",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 27,
                        "y" => 9,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 1,
                    "state" => "fallow",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 27,
                        "y" => 13,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
            ));
        
        $conn = $db->getDb();
        $query = "INSERT INTO userworlds (`uid`, `type`, `sizeX`, `sizeY`, `objects`, `messageManager`) VALUES ('{$uid}','{$type}','48','48','{$newWorld}','')";

        $conn->query($query);

        $db->destroy();

        return array(
            "uid" => $uid,
            'type' => $type,
            'sizeX' => 48,
            'sizeY' => 48,
            'objectsArray' => unserialize($newWorld),
            'messageManager' => array(),
            'creation' => date("Y-m-d h:i:s")
        );
    }