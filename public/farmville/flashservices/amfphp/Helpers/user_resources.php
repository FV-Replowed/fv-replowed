<?php
require_once AMFPHP_ROOTPATH . "Helpers/globals.php";

// user resources can help a user progress
class UserResources{
    public const XP_MAX = 2_147_400_000; // minimum points required to reach the highest level
    public const GOLD_MAX = 999_999_999; // specified by the engine

    public static function addXp($uid, $amount){
        global $db;

        $maxXp = self::XP_MAX;

        if (!is_numeric($uid) || !is_int($amount) || $amount <= 0){
            return false;
        }

        $conn = $db->getDb();
        $stmt = $conn->prepare("UPDATE usermeta SET xp = LEAST(xp + ?, ?) WHERE uid = ?");
        $stmt->bind_param("iis", $amount, $maxXp, $uid);
        $stmt->execute();
        $db->destroy();
        return true;
    }

    public static function adjustGold($uid, $delta){
        if (!is_numeric($uid) || !is_int($delta) || $delta === 0){
            return false;
        }

        return ($delta > 0) ? self::addGold($uid, $delta) : self::removeGold($uid, -$delta);
    }

    private static function addGold($uid, $amount){
        global $db;

        $maxGold = self::GOLD_MAX;

        $conn = $db->getDb();
        $stmt = $conn->prepare("UPDATE usermeta SET gold = LEAST(gold + ?, ?) WHERE uid = ?");
        $stmt->bind_param("iis", $amount, $maxGold, $uid);
        $stmt->execute();
        $db->destroy();
        return true;
    }

    private static function removeGold($uid, $amount){
        global $db;

        $conn = $db->getDb();
        $stmt = $conn->prepare("UPDATE usermeta SET gold = GREATEST(gold - ?, 0) WHERE uid = ?");
        $stmt->bind_param("is", $amount, $uid);
        $stmt->execute();
        $db->destroy();
        return true;
    }
}