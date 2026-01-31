<?php
require_once AMFPHP_ROOTPATH . "Helpers/globals.php";

// user resources can help a user progress
class UserResources{
    public const GOLD_FIELD = "gold";
    public const CASH_FIELD = "cash";
    public const XP_FIELD = "xp";
    public const GOLD_MAX = 999_999_999; // specified by the engine
    public const CASH_MAX = 99_999; // specified by the engine
    public const XP_MAX = 2_147_400_000; // minimum points required to reach the highest level

    private static function addResource($uid, $amount, $field, $max){
        global $db;

        if (!is_numeric($uid) || !is_int($amount) || $amount <= 0){
            return false;
        }

        $conn = $db->getDb();
        $stmt = $conn->prepare("UPDATE usermeta SET $field = LEAST($field + ?, ?) WHERE uid = ?");
        $stmt->bind_param("iis", $amount, $max, $uid);
        $stmt->execute();
        $db->destroy();
        return true;
    }

    private static function removeResource($uid, $amount, $field){
        global $db;

        if (!is_numeric($uid) || !is_int($amount) || $amount <= 0){
            return false;
        }

        $conn = $db->getDb();
        $stmt = $conn->prepare("UPDATE usermeta SET $field = GREATEST($field - ?, 0) WHERE uid = ?");
        $stmt->bind_param("is", $amount, $uid);
        $stmt->execute();
        $db->destroy();
        return true;
    }

    public static function addGold($uid, $amount){
        return self::addResource($uid, $amount, self::GOLD_FIELD, self::GOLD_MAX);
    }

    public static function addCash($uid, $amount){
        return self::addResource($uid, $amount, self::CASH_FIELD, self::CASH_MAX);
    }

    public static function addXp($uid, $amount){
        return self::addResource($uid, $amount, self::XP_FIELD, self::XP_MAX);
    }

    public static function removeGold($uid, $amount){
        return self::removeResource($uid, $amount, self::GOLD_FIELD);
    }

    public static function removeCash($uid, $amount){
        return self::removeResource($uid, $amount, self::CASH_FIELD);
    }
}