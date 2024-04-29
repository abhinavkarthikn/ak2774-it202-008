<?php
require(__DIR__ . "/../../partials/nav.php");

$db = getDB();
$driver_id = $_GET["driver_id"] ?? null;
$user_id = $_GET["user_id"] ?? null;

if ($driver_id && $user_id) {
    $query = "DELETE FROM `UserDrivers` WHERE driver_id = :driver_id AND user_id = :user_id";
    $params = [":driver_id" => $driver_id, ":user_id" => $user_id];
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Driver removed successfully", "success");
    } catch (PDOException $e) {
        error_log("Error removing driver: " . $e->getMessage());  //ak2774, 4/29/24
        flash("Error removing driver", "danger");
    }
}

redirect("my_drivers.php");
?>
