<?php

session_start();
require_once(__DIR__ . "/../../../lib/functions.php");
if(!has_role("Admin")){
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}

$id=se($_GET, "id", -1, false);
if($id<1){
    flash("Invalid id passed to delete", "danger");
    die(header("Location: " . get_url("admin/list_drivers.php")));
}

$db=getDB();
$query="DELETE FROM `Drivers` WHERE id=:id";
try{
    $stmt=$db->prepare($query);
    $stmt->execute([":id"=>$id]);
    flash("Deleted record with id $id", "success");
}
catch(PDOException $e){
    error_log("Error deleting driver $id" . var_export($e, true));
    flash("Error deleting record", "danger");
    
}
die(header("Location: " . get_url("admin/list_drivers.php")));