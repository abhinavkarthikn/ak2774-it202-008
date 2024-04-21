<?php

require(__DIR__ . "/../../../lib/functions.php");
session_start();
if(isset($_GET["driver_id"]) && is_logged_in()){
    $db=getDB();
    $query= "INSERT INTO `UserDrivers` (user_id, driver_id) VALUES(:user_id, :driver_id)";
    try{
        $stmt=$db->prepare($query);
        $stmt->execute([":user_id"=>get_user_id(), ":driver_id"=>$_GET["driver_id"]]);
        flash("Driver added to your list", "success");
    }
    catch(PDOException $e){
        if($e->errorInfo[1] === 1062){
            flash("Driver already added to your list", "danger");
        }
        else{
            flash("Unhandled error occured", "danger");
        }
        error_log("Error viewing driver: " . var_export($e, true));
    }
}

redirect("drivers.php");