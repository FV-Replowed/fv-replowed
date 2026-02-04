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

        return ($meta != null) ? $meta["meta_value"] : false;
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
                $item = $result->fetch_assoc();
                $db->destroy();

                return unserialize($item["data"]);
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

        $size = 48; // once expansions are fixed, the schema default should be used
        $messageManager = "";

        // Unix timestamp in milliseconds
        $plantTime = (float) ((time() * 1000) - 172800000); // pretend 2 days elapsed
        
        $newWorld = serialize(array(
            0 => 
            (object) array(
                'plantTime' => $plantTime,
                'position' => 
                (object) array(
                'x' => 27,
                'z' => 0,
                'y' => 13,
                ),
                'isBigPlot' => false,
                'direction' => 0,
                'isJumbo' => true,
                'deleted' => false,
                'tempId' => -1,
                'className' => 'Plot',
                'state' => 'fallow',
                'instanceDataStoreKey' => NULL,
                'components' => 
                (object) array(
                ),
                'isProduceItem' => false,
                'id' => 1,
                'itemName' => NULL,
            ),
            1 => 
            (object) array(
                'plantTime' => $plantTime,
                'position' => 
                (object) array(
                'x' => 27,
                'z' => 0,
                'y' => 9,
                ),
                'isBigPlot' => false,
                'direction' => 0,
                'isJumbo' => true,
                'deleted' => false,
                'tempId' => -1,
                'className' => 'Plot',
                'state' => 'fallow',
                'instanceDataStoreKey' => NULL,
                'components' => 
                (object) array(
                ),
                'isProduceItem' => false,
                'id' => 2,
                'itemName' => NULL,
            ),
            2 => 
            (object) array(
                'plantTime' => $plantTime, // finish growing now
                'position' => 
                (object) array(
                'x' => 19,
                'z' => 0,
                'y' => 9,
                ),
                'isBigPlot' => false,
                'direction' => 0,
                'isJumbo' => false,
                'deleted' => false,
                'tempId' => -1,
                'className' => 'Plot',
                'state' => 'planted',
                'instanceDataStoreKey' => NULL,
                'components' => 
                (object) array(
                ),
                'isProduceItem' => false,
                'id' => 3,
                'itemName' => 'eggplant',
            ),
            3 => 
            (object) array(
                'plantTime' => $plantTime,
                'position' => 
                (object) array(
                'x' => 19,
                'z' => 0,
                'y' => 13,
                ),
                'isBigPlot' => false,
                'direction' => 0,
                'isJumbo' => false,
                'deleted' => false,
                'tempId' => -1,
                'className' => 'Plot',
                'state' => 'planted',
                'instanceDataStoreKey' => NULL,
                'components' => 
                (object) array(
                ),
                'isProduceItem' => false,
                'id' => 4,
                'itemName' => 'eggplant',
            ),
            4 => 
            (object) array(
                'plantTime' => NAN,
                'position' => 
                (object) array(
                'x' => 23,
                'z' => 0,
                'y' => 9,
                ),
                'isBigPlot' => false,
                'direction' => 0,
                'isJumbo' => false,
                'deleted' => false,
                'tempId' => -1,
                'className' => 'Plot',
                'state' => 'plowed',
                'instanceDataStoreKey' => NULL,
                'components' => 
                (object) array(
                ),
                'isProduceItem' => false,
                'id' => 5,
                'itemName' => NULL,
            ),
            5 => 
            (object) array(
                'plantTime' => NAN,
                'position' => 
                (object) array(
                'x' => 23,
                'z' => 0,
                'y' => 13,
                ),
                'isBigPlot' => false,
                'direction' => 0,
                'isJumbo' => false,
                'deleted' => false,
                'tempId' => -1,
                'className' => 'Plot',
                'state' => 'plowed',
                'instanceDataStoreKey' => NULL,
                'components' => 
                (object) array(
                ),
                'isProduceItem' => false,
                'id' => 6,
                'itemName' => NULL,
            ),
        ));
        
        // only checking if the serialization was successful JUST IN CASE
        if (is_numeric($uid) && is_string($type) && $type !== "" && is_string($newWorld)){
            $conn = $db->getDb();
            $stmt = $conn->prepare("INSERT INTO userworlds (uid, type, sizeX, sizeY, objects, messageManager) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiss", $uid, $type, $size, $size, $newWorld, $messageManager);
            $stmt->execute();
            $db->destroy();
        }

        return array(
            "uid" => $uid,
            'type' => $type,
            'sizeX' => $size,
            'sizeY' => $size,
            'objectsArray' => unserialize($newWorld),
            'messageManager' => array(),
            'creation' => date("Y-m-d h:i:s")
        );
    }