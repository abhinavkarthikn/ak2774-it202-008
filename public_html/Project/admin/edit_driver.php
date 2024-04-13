<?php

require(__DIR__ . "/../../../partials/nav.php");

if(!has_role("Admin")){
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}
?>
<?php

//TODO handle driver fetch
if(isset($_POST["action"])){
    $action=$_POST["action"];
    $name= se($_POST, "name", "", false);
    $info=[];
    if($name){
        if($action==="fetch"){
            $result=fetch_driver($name);
            error_log("Data from API" . var_export($result, true));
            if($result){
                $info=$result;
            }

        }
        else if($action==="create"){
            foreach($_POST as $k=>$v){
                if(!in_array($k,["name", "abbr", "image", "nationality", "country", "birthdate", "birthplace", "number", "grands_prix_entered", "world_championships", "podiums", "highest_race_finish", "highest_grid_position", "career_points" ] )){
                    unset($_POST[$k]);
                }
                $info=$_POST;
            }

            }
        }
    else{
        flash("You must provide a name", "warning");
    }
    //Insert Data
    $db=getDB();
    $query="INSERT INTO `Drivers` ";
    $columns=[];
    $params=[];
    foreach($info as $k => $v) {
            array_push($columns, "$k");
            $params[":$k"] = $v;   
    }


    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",",array_keys($params)) . ")";
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));



    try{
        $stmt=$db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record" . $db->lastInsertId(), "success");
    }
    catch(PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
    }
}

//TODO handle manual create driver

?>
<class="container-fluid">
    <h3>Edit Driver</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-warning" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-warning" href="#" onclick="switchTab('fetch')">Create</a>
    </ul>
    <div id="fetch" class="tab-target">
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "name", "placeholder" => "Driver name", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
        <?php render_button(["text" => "Search", "type" => "submit",]); ?>
    </form>
    </div>

    <div id="create" style="display: none;" class="tab-target">
    <form method="POST">
        <?php render_input(["type" => "text", "name" => "name", "placeholder" => "Driver Name", "label" => "Driver Name", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "abbr", "placeholder" => "Drive Abbr", "label" => "Driver Abbr", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "image", "placeholder" => "Image URL", "label" => "Image URL", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "nationality", "placeholder" => "Nationality", "label" => "Nationality", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "country", "placeholder" => "Country", "label" => "Country", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "date", "name" => "birthdate", "placeholder" => "DOB", "label" => "DOB", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "birthplace", "placeholder" => "Birthplace", "label" => "Birthplace", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "number", "placeholder" => "Driver Number", "label" => "Driver Number", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "grands_prix_entered", "placeholder" => "GPs Entered", "label" => "GPs Entered", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "world_championships", "placeholder" => "WCs", "label" => "World Championships", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "podiums", "placeholder" => "Podiums", "label" => "Podiums", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "highest_race_finish", "placeholder" => "Highest race finish", "label" => "Highest Race Finish", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "highest_grid_position", "placeholder" => "Highest grid position", "label" => "Highest Grid Position", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "number", "name" => "career_points", "placeholder" => "Career Points", "label" => "Career Points", "rules" => ["required" => "required"]]); ?>

        <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text"=> "Create"]); ?>
    </form>
    </div>
</div>

<script>
    function switchTab(tab){
        let target=document.getElementById(tab);
        if(target){
            let eles=document.getElementsByClassName("tab-target");
            for(let ele of eles){
                ele.style.display=(ele.id===tab)? "none" : "block";
            }
        }
    }
</script>    

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>