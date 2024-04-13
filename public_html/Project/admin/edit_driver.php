<?php

require(__DIR__ . "/../../../partials/nav.php");

if(!has_role("Admin")){
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}
?>
<?php
$id = se($_GET, "id", -1, false);
//TODO handle driver fetch
if(isset($_POST["name"])){
    foreach($_POST as $k=>$v){
        if(!in_array($k,["name", "abbr", "image", "nationality", "country", "birthdate", "birthplace", "number", "grands_prix_entered", "world_championships", "podiums", "highest_race_finish", "highest_grid_position", "career_points" ] )){
            unset($_POST[$k]);
        }
        $info=$_POST;
    }
    //Insert Data
    $db=getDB();
    $query="UPDATE `Drivers` SET ";

    $params=[];
    foreach($info as $k => $v) {
            
            if($params){
                $query .= ",";
            }
            //be sure $k is trusted as this is a source of sql injection
            $query .= "$k=:$k";
            $params[":$k"] = $v;   
    }


    $query .= " WHERE id=:id";
    $params[":id"]=$id;
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));



    try{
        $stmt=$db->prepare($query);
        $stmt->execute($params);
        flash("Updated record", "success");
    }
    catch(PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
    }
}

$driver=[];
if($id>-1){
    //fetch
    $db=getDB();
    $query="SELECT name, abbr, image, nationality, country, birthdate, birthplace, number, grands_prix_entered, world_championships, podiums, highest_race_finish, highest_grid_position, career_points FROM `Drivers` WHERE id=:id";
    
    try{
        $stmt=$db->prepare($query);
        $stmt->execute([":id"=>$id]);
        $r=$stmt->fetch();
        if($r){
            $driver=$r;
        }
    }
    catch(PDOException $e){
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
}
else{
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("admin/list_drivers.php")));
}
if($driver){
    $form = [["type" => "text", "name" => "name", "placeholder" => "Driver Name", "label" => "Driver Name", "rules" => ["required" => "required"]],
    ["type" => "text", "name" => "abbr", "placeholder" => "Drive Abbr", "label" => "Driver Abbr", "rules" => ["required" => "required"]],
    ["type" => "text", "name" => "image", "placeholder" => "Image URL", "label" => "Image URL", "rules" => ["required" => "required"]],
    ["type" => "text", "name" => "nationality", "placeholder" => "Nationality", "label" => "Nationality", "rules" => ["required" => "required"]],
    ["type" => "text", "name" => "country", "placeholder" => "Country", "label" => "Country", "rules" => ["required" => "required"]],
    ["type" => "date", "name" => "birthdate", "placeholder" => "DOB", "label" => "DOB", "rules" => ["required" => "required"]],
    ["type" => "text", "name" => "birthplace", "placeholder" => "Birthplace", "label" => "Birthplace", "rules" => ["required" => "required"]],
    ["type" => "number", "name" => "number", "placeholder" => "Driver Number", "label" => "Driver Number", "rules" => ["required" => "required"]],
    ["type" => "number", "name" => "grands_prix_entered", "placeholder" => "GPs Entered", "label" => "GPs Entered", "rules" => ["required" => "required"]],
    ["type" => "number", "name" => "world_championships", "placeholder" => "WCs", "label" => "World Championships", "rules" => ["required" => "required"]],
    ["type" => "number", "name" => "podiums", "placeholder" => "Podiums", "label" => "Podiums", "rules" => ["required" => "required"]],
    ["type" => "number", "name" => "highest_race_finish", "placeholder" => "Highest race finish", "label" => "Highest Race Finish", "rules" => ["required" => "required"]],
    ["type" => "number", "name" => "highest_grid_position", "placeholder" => "Highest grid position", "label" => "Highest Grid Position", "rules" => ["required" => "required"]],
    ["type" => "number", "name" => "career_points", "placeholder" => "Career Points", "label" => "Career Points", "rules" => ["required" => "required"]]];

    $keys=array_keys($driver);
    foreach($form as $k=>$v){
        if(in_array($v["name"], $keys)){
            $form[$k]["value"]=$driver[$v["name"]];
        }
    }
}

//TODO handle manual create driver

?>
<div class="container-fluid">
    <h3>Edit Driver</h3>
    <div>
        <a href="<?php echo get_url("admin/list_drivers.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach($form as $k=>$v){
            render_input($v);
        } ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text"=> "Update"]); ?>
    </form>
</div> 

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>