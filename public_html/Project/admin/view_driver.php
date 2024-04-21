<?php

require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to view this page", "warning");
    redirect("home.php");
}

$id = se($_GET, "id", -1, false);

$driver=[];
if($id>-1){
    $db=getDB();
    $query="SELECT name, abbr, image, nationality, country, birthdate, birthplace, number, grands_prix_entered, 
    world_championships, podiums, highest_race_finish, highest_grid_position, career_points FROM `Drivers` WHERE id=:id";
    try{
        $stmt=$db->prepare($query);      //ak2774, 4/15/2024
        $stmt->execute([":id"=>$id]);
        $driver=$stmt->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        error_log("Error fetching driver data: " . var_export($e, true));
        flash("Error fetching driver data", "danger");
    }
}
else{
    flash("Invalid id passed", "danger");
    redirect("admin/list_drivers.php");
}
?>
<div>
    <a href="<?php echo get_url("admin/list_drivers.php"); ?>" class="btn btn-secondary">Back</a>
    <a href="<?php echo get_url("admin/edit_driver.php?id=" . $id); ?>" class="btn btn-primary">Edit</a>
    <a href="<?php echo get_url("admin/delete_driver.php?id=" . $id); ?>" class="btn btn-danger">Delete</a>
</div>
<div class="container mt-4 d-flex justify-content-center">
    
    <?php render_driver_card($driver); ?>
</div>

<?php require(__DIR__ . "/../../../partials/flash.php");?>
