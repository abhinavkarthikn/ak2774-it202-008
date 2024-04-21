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
    
    <?php render_driver_card($driver); ?>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>
