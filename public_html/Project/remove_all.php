<?php
require(__DIR__ . "/../../partials/nav.php");

$db = getDB();

$username = $_GET["username"];
if ($username) {
    $query = "DELETE FROM UserDrivers WHERE user_id IN (SELECT id FROM Users WHERE username LIKE :username)";
    $params = [":username" => "%$username%"];
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("All drivers favorited by user(s)  removed successfully", "success");
    } catch (PDOException $e) {
        error_log("Error removing drivers: " . $e->getMessage());
        flash("Error removing drivers", "danger");
    }
}

redirect("my_drivers.php");
?>
