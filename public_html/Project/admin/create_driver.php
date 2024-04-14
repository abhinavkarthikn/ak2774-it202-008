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
                $info["is_api"] = 1;
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
        //flash("You must provide a name", "warning");
        $name=se($info, "name", "", false);
        if(empty($name)){
            flash("You must provide a name", "warning");
        }
        $abbr=se($info, "abbr", "", false);
        if(empty($abbr)){
            flash("You must provide an abbreviation", "warning");
        }
        $image=se($info, "image", "", false);
        if(empty($image)){
            flash("You must provide an image", "warning");
        }
        $nationality=se($info, "nationality", "", false);
        if(empty($nationality)){
            flash("You must provide a nationality", "warning");
        }
        $country=se($info, "country", "", false);
        if(empty($country)){
            flash("You must provide a country", "warning");
        }
        $birthdate=se($info, "birthdate", "", false);
        if(empty($birthdate)){
            flash("You must provide a birthdate", "warning");
        }
        $birthplace=se($info, "birthplace", "", false);
        if(empty($birthplace)){
            flash("You must provide a birthplace", "warning");
        }
        $number=se($info, "number", "", false);
        if(empty($number)){
            flash("You must provide a number", "warning");
        }
        $grands_prix_entered=se($info, "grands_prix_entered", "", false);
        if(empty($grands_prix_entered)){
            flash("You must provide a number of GPs entered", "warning");
        }
        $world_championships=se($info, "world_championships", "", false);
        if(empty($world_championships)){
            flash("You must provide a number of world championships", "warning");
        }
        $podiums=se($info, "podiums", "", false);
        if(empty($podiums)){
            flash("You must provide a number of podiums", "warning");
        }
        $highest_race_finish=se($info, "highest_race_finish", "", false);
        if(empty($highest_race_finish)){
            flash("You must provide a highest race finish", "warning");
        }
        $highest_grid_position=se($info, "highest_grid_position", "", false);
        if(empty($highest_grid_position)){
            flash("You must provide a highest grid position", "warning");
        }
        $career_points=se($info, "career_points", "", false);
        if(empty($career_points)){
            flash("You must provide a number of career points", "warning");
        }

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
        flash("Inserted record " . $db->lastInsertId(), "success");
    }
    catch(PDOException $e){
        if($e->errorInfo[1] === 1062){
            flash("Driver already exists, please enter a different driver", "warning");
        }
        else{
            error_log("Something broke with the query" . var_export($e, true));
            flash("An error occured", "danger");
        }
    }
}

//TODO handle manual create driver

?>
<div class="container-fluid">
    <h3>Create or Fetch Driver</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-warning" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-warning" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "name", "placeholder" => "Driver name", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
        <?php render_button(["text" => "Search", "type" => "submit",]); ?>
    </form>
    </div>

    <div id="create" style="display: none;" class="tab-target">
    <form method="POST" onsubmit="return validate(this);">
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

    function validate(form){
        let isValid=true;
        let name=form.name.value;
        if(name===""){
            flash("Name must not be empty [js]", "danger");
            isValid=false;
        }
        let abbr=form.abbr.value;
        if(abbr===""){
            flash("Abbreviation must not be empty [js]", "danger");
            isValid=false;
        }
        let image=form.image.value;
        if(image===""){
            flash("Image must not be empty [js]", "danger");
            isValid=false;
        }
        let nationality=form.nationality.value;
        if(nationality===""){
            flash("Nationality must not be empty [js]", "danger");
        }
        let country=form.country.value;
        if(country===""){
            flash("Country must not be empty [js]", "danger");
            isValid=false;
        }
        let birthdate=form.birthdate.value;
        if(birthdate===""){
            flash("Birthdate must not be empty [js]", "danger");
            isValid=false;
        }
        let birthplace=form.birthplace.value;
        if(birthplace===""){
            flash("Birthplace must not be empty [js]", "danger");
            isValid=false;
        }
        let number=form.number.value;
        if(number===""){
            flash("Number must not be empty [js]", "danger");
            isValid=false;
        }
        let grands_prix_entered=form.grands_prix_entered.value;
        if(grands_prix_entered===""){
            flash("Grands Prix Entered must not be empty [js]", "danger");
            isValid=false;
        }
        let world_championships=form.world_championships.value;
        if(world_championships===""){
            flash("World Championships must not be empty [js]", "danger");
            isValid=false;
        }
        let podiums=form.podiums.value;
        if(podiums===""){
            flash("Podiums must not be empty [js]", "danger");
            isValid=false;
        }
        let highest_race_finish=form.highest_race_finish.value;
        if(highest_race_finish===""){
            flash("Highest race finish must not be empty [js]", "danger");
            isValid=false;
        }
        let highest_grid_position=form.highest_grid_position.value;
        if(highest_grid_position===""){
            flash("Highest grid position must not be empty [js]", "danger");
            isValid=false;
        }
        let career_points=form.career_points.value;
        if(career_points===""){
            flash("Career points must not be empty [js]", "danger");
            isValid=false;
        }
        return isValid;
    
    }

</script>  




<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>