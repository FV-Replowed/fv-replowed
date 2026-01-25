<?php

class WorldService
{
    public static function performAction($playerObj, $request, $market)
    {
        switch ($request->params[0]) {
            case "plow":
            case "plant":
            case "move":
            case "place":
            case "sell":
            case "harvest":
            case "clear":
                $retId = $playerObj->setWorld($request->params[1], $request->params[0]);
                $market->newTransaction($request->params[0], $request->params[1]);
                $data["id"] = $retId;
                $data["data"] = array(
                    "id" => $retId
                );
                break;
        }
        return $data;
    }

    public static function loadOwnWorld($playerObj, $request)
    {
        $loadType = $request->params[0] == "" ? 'farm' : $request->params[0];
        $travelWorld = getWorldByType($playerObj->getUid(), $loadType);
        $data["data"] = array(
            "user" => array(
                "currentWorldType" => $travelWorld["type"],
                "worldSummaryData" => array(
                    $travelWorld["type"] => array(
                        "firstLoaded" => strtotime($travelWorld['creation']),
                        "lastLoaded" => strtotime(date("Y-m-d h:i:s"))
                    )

                ),
                "player" => array(
                    "featureCredits" => array(
                        "farm" => array(),
                        "england" => array()
                    )
                )
            ),
            "world" => $travelWorld
        );

        set_meta($playerObj->getUid(), 'currentWorldType', $travelWorld["type"]);

        return $data;
    }

    public static function loadNeighborWorld($playerObj, $request){
        $travelWorld = getWorldByType($request->params[0]);
        $data["data"] = array(
            "user" => array(
                "ugcItemData" => [
                    // "U" => "",
                    // "I" => "",
                    // "N" => "",
                    // "D" => "",
                ],
                "instanceDataStore" => [

                ]
            ),
            "world" => $travelWorld
        );

        return $data;
    }
}
