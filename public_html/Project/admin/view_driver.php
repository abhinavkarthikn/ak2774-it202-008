<?php

require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false);

$driverData=[];
if($id>-1){
    $db=getDB();
    $query="SELECT name, abbr, image, nationality, country, birthdate, birthplace, number, grands_prix_entered, 
    world_championships, podiums, highest_race_finish, highest_grid_position, career_points FROM `Drivers` WHERE id=:id";
    try{
        $stmt=$db->prepare($query);      //ak2774, 4/15/2024
        $stmt->execute([":id"=>$id]);
        $driverData=$stmt->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        error_log("Error fetching driver data: " . var_export($e, true));
        flash("Error fetching driver data", "danger");
    }
}
else{
    flash("Invalid id passed", "danger");
    die(header("Location: " . get_url("admin/list_drivers.php")));
}
?>
<div>
    <a href="<?php echo get_url("admin/list_drivers.php"); ?>" class="btn btn-secondary">Back</a>
    <a href="<?php echo get_url("admin/edit_driver.php?id=" . $id); ?>" class="btn btn-primary">Edit</a>
    <a href="<?php echo get_url("admin/delete_driver.php?id=" . $id); ?>" class="btn btn-danger">Delete</a>
</div>
<div class="container mt-4 d-flex justify-content-center">
    
    <div class="card" style="width: 18rem;">
        <?php if(!empty($driverData["image"])):?>
            <img src="<?php echo $driverData["image"];?>" class="card-img-top" alt="Driver Image">
        <?php endif;?>

        <div class="card-body">
            <h5 class="card-title"><?php echo($driverData["name"]);?></h5>
            <p class="card-text">Driver Number: <?php safer_echo($driverData["number"]);  //ak2774, 4/15/2024?></p>
            <p class="card-text">Birthdate: <?php safer_echo($driverData["birthdate"]);?></p>
            <p class="card-text">Country: <?php safer_echo($driverData["country"]);?></p>
            <p class="card-text">GP's Entered: <?php safer_echo($driverData["grands_prix_entered"]);?></p>
            <p class="card-text">World Championships: <?php safer_echo($driverData["world_championships"]);?></p>
            <p class="card-text">Wins: <?php safer_echo($driverData["highest_race_finish"]);?></p>
            <p class="card-text">Podiums: <?php safer_echo($driverData["podiums"]);?></p>
            <p class="card-text">Career Points: <?php safer_echo($driverData["career_points"]);?></p>
        </div>
    </div>
</div>

<?php require(__DIR__ . "/../../../partials/flash.php");?>
