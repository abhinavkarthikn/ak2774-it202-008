<?php
require(__DIR__ . "/../../partials/nav.php");

$db=getDB();
$query = "DELETE FROM `UserDrivers` WHERE driver_id = :driver_id";
$params = [ ":driver_id" => $_GET["driver_id"]];
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Driver selected removed successfully", "success");
        redirect("my_drivers.php");
    } catch (PDOException $e) {
        error_log("Error removing driver: " . $e->getMessage());
        flash("Error removing driver", "danger");
    }


redirect("my_drivers.php");