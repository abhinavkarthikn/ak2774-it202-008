<?php

session_start();
require_once(__DIR__ . "/../../../lib/functions.php");
if(!has_role("Admin")){
    flash("You do not have permission to view this page", "warning");
    redirect("home.php");
}

$id=se($_GET, "id", -1, false);
if($id<1){
    flash("Invalid id passed to delete", "danger");
    redirect("admin/list_drivers.php");
}

$db=getDB();
$query="DELETE FROM `Drivers` WHERE id=:id";  //ak2774, 4/15/2024
try{
    $stmt=$db->prepare($query);
    $stmt->execute([":id"=>$id]);
    flash("Deleted record with id $id", "success");
}
catch(PDOException $e){
    error_log("Error deleting driver $id" . var_export($e, true));
    flash("Error deleting record", "danger");
    
}
redirect("admin/list_drivers.php");