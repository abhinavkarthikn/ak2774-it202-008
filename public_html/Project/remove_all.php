<?php
require(__DIR__ . "/../../partials/nav.php");

$db = getDB();

$query = "DELETE FROM UserDrivers WHERE 1=1";
$params = [];

$username = $_GET["username"];
if (!empty($username)) {
    $query .= " AND user_id IN (SELECT id FROM Users WHERE username LIKE :username)";
    $params[":username"] = "%$username%";
}

$name = $_GET["name"];
if (!empty($name)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE name LIKE :name)";
    $params[":name"] = "%$name%";
}

$country = $_GET["country"];
if (!empty($country)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE country LIKE :country)";
    $params[":country"] = "%$country%";
}

$number = $_GET["number"];
if (!empty($number)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE number=:number)";
    $params[":number"] = $number;
}

$grands_prix_entered_min = $_GET["grands_prix_entered_min"];
if (!empty($grands_prix_entered_min)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE grands_prix_entered >= :grands_prix_entered_min)";
    $params[":grands_prix_entered_min"] = $grands_prix_entered_min;
}

$grands_prix_entered_max = $_GET["grands_prix_entered_max"];
if (!empty($grands_prix_entered_max)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE grands_prix_entered <= :grands_prix_entered_max)";
    $params[":grands_prix_entered_max"] = $grands_prix_entered_max;
}

$world_championships_min = $_GET["world_championships_min"];
if (!empty($world_championships_min)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE world_championships >= :world_championships_min)";
    $params[":world_championships_min"] = $world_championships_min;
}

$world_championships_max = $_GET["world_championships_max"];
if (!empty($world_championships_max)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE world_championships <= :world_championships_max)";
    $params[":world_championships_max"] = $world_championships_max;
}

$highest_race_finish_min = $_GET["highest_race_finish_min"];
if (!empty($highest_race_finish_min)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE highest_race_finish >= :highest_race_finish_min)";
    $params[":highest_race_finish_min"] = $highest_race_finish_min;
}

$highest_race_finish_max = $_GET["highest_race_finish_max"];
if (!empty($highest_race_finish_max)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE highest_race_finish <= :highest_race_finish_max)";
    $params[":highest_race_finish_max"] = $highest_race_finish_max;
}

$podiums_min = $_GET["podiums_min"];
if (!empty($podiums_min)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE podiums >= :podiums_min)";
    $params[":podiums_min"] = $podiums_min;
}

$podiums_max = $_GET["podiums_max"];
if (!empty($podiums_max)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE podiums <= :podiums_max)";
    $params[":podiums_max"] = $podiums_max;
}

$career_points_min = $_GET["career_points_min"];
if (!empty($career_points_min)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE career_points >= :career_points_min)";
    $params[":career_points_min"] = $career_points_min;
}

$career_points_max = $_GET["career_points_max"];
if (!empty($career_points_max)) {
    $query .= " AND driver_id IN (SELECT id FROM Drivers WHERE career_points <= :career_points_max)";
    $params[":career_points_max"] = $career_points_max;
}

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    flash("All drivers favorited by user(s) removed successfully", "success");
} catch (PDOException $e) {
    error_log("Error removing drivers: " . $e->getMessage());
    flash("Error removing drivers", "danger");
}

redirect("my_drivers.php");
?>
