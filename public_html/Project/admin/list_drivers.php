<?php

require(__DIR__ . "/../../../partials/nav.php");

if(!has_role("Admin")){
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}


$query="SELECT id, name, abbr, image, nationality, country, birthdate, birthplace, number, grands_prix_entered, world_championships, podiums, highest_race_finish, highest_grid_position, career_points FROM `Drivers` ORDER BY created DESC LIMIT 25";
$db=getDB();
$stmt=$db->prepare($query);
$results=[];
try{
    $stmt->execute();
    $r=$stmt->fetchAll();
    if($r){
        $results=$r;
    }
}
catch(PDOException $e){
    error_log("Error detching drivers " . var_export($e, true));
    flash("Unhandled error occured ", "danger");
}

$table=["data"=>$results, "title"=>"All Drivers", "ignored_columns"=>["id"], "edit_url"=>get_url("admin/edit_driver.php")];
?>

<div class="container-fluid">
    <h3>Drivers</h3>
    <?php render_table($table); ?>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>