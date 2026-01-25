<?php
    require_once AMFPHP_ROOTPATH . "Helpers/globals.php";
    /**
     * Get User Metadata
     * 
     * @param string $uid User ID
     * @param string $meta_key Meta Key
     * @return string The value of the meta field
     *                False for invalid $uid of non found $meta_key
     */
    function get_meta($uid, $meta_key){
        global $db;

        $meta = [];

        if (is_numeric($uid) && is_string($meta_key) && $meta_key !== ""){
            $conn = $db->getDb();
            $stmt = $conn->prepare("SELECT meta_value FROM playermeta WHERE meta_key = ? AND uid = ?");
            $stmt->bind_param("ss", $meta_key, $uid);
            $stmt->execute();
            // var_dump($conn->query($query));
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0){
                $meta = $result->fetch_assoc();
            }

            $db->destroy();
        }

        return $meta["meta_value"] ?? false;
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

        $meta_rec = get_meta($uid, $meta_key); // params validated inside
        $conn = $db->getDb();
        
        if (is_string($meta_value)){
            if ($meta_rec){
                $stmt = $conn->prepare("UPDATE playermeta SET meta_value = ? WHERE uid = ? AND meta_key = ?");
                $stmt->bind_param("sss", $meta_value, $uid, $meta_key);
                $stmt->execute();
            }else{
                $stmt = $conn->prepare("INSERT INTO playermeta (uid, meta_key, meta_value) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $uid, $meta_key, $meta_value);
                $stmt->execute();
            }

            $db->destroy();
        }
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

        if (is_string($itemName) && $itemName !== ""){
            if ($method === "db"){
                $conn = $db->getDb();
                $stmt = $conn->prepare("SELECT * FROM items WHERE name = ?");
                $stmt->bind_param("s", $itemName);
                $stmt->execute();
                $result = $stmt->get_result();
                $item = $result->fetch_assoc() ?? [];
                $db->destroy();

                return unserialize($item['data'] ?? null);
            }

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

    /*
    function getWorldByUid($uid){
        return getWorldByType($uid);
    }
    */
    function getWorldByType($uid, $type = "farm"){
        global $db;

        $worldData = [];

        if (is_numeric($uid) && is_string($type) && $type !== ""){
            $conn = $db->getDb();
            $stmt = $conn->prepare("SELECT * FROM userworlds WHERE type = ? AND uid = ?");
            $stmt->bind_param("ss", $type, $uid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0){
                // no point in validating further
                // if the row's contents are invalid, loading SHOULD fail
                $row = $result->fetch_assoc();
                $worldData["type"] = $row["type"];
                $worldData["sizeX"] = $row["sizeX"];
                $worldData["sizeY"] = $row["sizeY"];
                $worldData["objectsArray"] = unserialize($row["objects"]);
                $worldData["creation"] = $row["created_at"];
                $worldData["messageManager"] = array();
            }else{
                $worldData = createWorldByType($uid, $type);
            }

            $db->destroy();
        }
        
        return $worldData;
    }

    function createWorldByType($uid, $type = "farm" ){
        global $db;

        $defaultSize = 48;
        $defaultMessageManager = "";
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
                    "id" => 6,
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
        
        // only checking if the serialization was successful JUST IN CASE
        if (is_numeric($uid) && is_string($type) && $type !== "" && is_string($newWorld)){
            $conn = $db->getDb();
            $stmt = $conn->prepare("INSERT INTO userworlds (uid, type, sizeX, sizeY, objects, messageManager) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiss", $uid, $type, $defaultSize, $defaultSize, $newWorld, $defaultMessageManager);
            $stmt->execute();
            $db->destroy();
        }

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