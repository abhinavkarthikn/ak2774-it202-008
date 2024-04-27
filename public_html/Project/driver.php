<?php
require(__DIR__ . "/../../partials/nav.php");

?>

<?php
$id=se($_GET, "id", -1, false);

$driver=[];
if($id>-1){
    $db=getDB();
    $query="SELECT * FROM Drivers WHERE id=:id";

    try{
        $stmt=$db->prepare($query);
        $stmt->execute([":id"=>$id]);
        $r=$stmt->fetch();
        if($r){
            $driver=$r;
        }
    }
    catch(PDOException $e){
        error_log("Error fetching driver:" . var_export($e, true));
        flash("Error fetching driver", "danger");
    }
}

else{
    flash("Invalid driver ID", "danger");
    redirect("drivers.php");
}

foreach($driver as $key=>$value){
    if(is_null($value)){
        $driver[$key]="N/A";
    }
}

?>

<div>
    <a href="<?php echo get_url("drivers.php"); ?>" class="btn btn-secondary">Back</a>
</div>

<div class="container mt-4 d-flex justify-content-center">
    <div class="card" style="width: 18rem;">
        <?php if(!empty($driver["image"])):?>
            <img src="<?php echo $driver["image"];?>" class="card-img-top" alt="Driver Image">
        <?php endif;?>

        <div class="card-body">
            <h5 class="card-title"><?php echo($driver["name"]);?></h5>
            <p class="card-text">Driver Number: <?php safer_echo($driver["number"]);?></p>
            <p class="card-text">Birthdate: <?php safer_echo($driver["birthdate"]);?></p>
            <p class="card-text">Country: <?php safer_echo($driver["country"]);?></p>
            <p class="card-text">GP's Entered: <?php safer_echo($driver["grands_prix_entered"]);?></p>
            <p class="card-text">World Championships: <?php safer_echo($driver["world_championships"]);?></p>
            <p class="card-text">Wins: <?php safer_echo($driver["highest_race_finish"]);?></p>
            <p class="card-text">Podiums: <?php safer_echo($driver["podiums"]);?></p>
            <p class="card-text">Career Points: <?php safer_echo($driver["career_points"]);?></p>
            <a class="btn btn-secondary" href="<?php echo get_url('api/detail_driver.php?driver_id=' .$driver["id"]); ?>" class="card-link">Add Driver</a>
            
            
        </div>
    </div>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>
