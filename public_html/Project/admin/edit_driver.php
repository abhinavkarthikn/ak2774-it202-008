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

    $name=se($info, "name", "", false);
    if(empty($name)){
        flash("Name must not be empty", "warning");
    }
    $abbr=se($info, "abbr", "", false);
    if(empty($abbr)){
        flash("Abbr must not be empty", "warning");
    }
    $image=se($info, "image", "", false);
    if(empty($image)){
        flash("Image must not be empty", "warning");
    }
    $nationality=se($info, "nationality", "", false);
    if(empty($nationality)){
        flash("Nationality must not be empty", "warning");
    }
    $country=se($info, "country", "", false);
    if(empty($country)){
        flash("Country must not be empty", "warning");
    }
    $birthdate=se($info, "birthdate", "", false);
    if(empty($birthdate)){
        flash("Birthdate must not be empty", "warning");
    }
    $birthplace=se($info, "birthplace", "", false);
    if(empty($birthplace)){
        flash("Birthplace must not be empty", "warning");
    }
    $number=se($info, "number", "", false);
    if(empty($number)){
        flash("Number must not be empty", "warning");
    }
    $grands_prix_entered=se($info, "grands_prix_entered", "", false);
    if(empty($grands_prix_entered)){
        flash("GPs Entered must not be empty", "warning");
    }
    $world_championships=se($info, "world_championships", "-1", false);
    if(empty($world_championships) && $world_championships<-1){
        flash("World Championships must not be empty", "warning");
    }
    $podiums=se($info, "podiums", "-1", false);
    if(empty($podiums) && $podiums<-1){
        flash("Podiums must not be empty", "warning");
    }
    $highest_race_finish=se($info, "highest_race_finish", "-1", false);
    if(empty($highest_race_finish) && $highest_race_finish<-1){
        flash("Wins must not be empty", "warning");
    }
    $highest_grid_position=se($info, "highest_grid_position", "", false);
    if(empty($highest_grid_position)){
        flash("Highest Grid Position must not be empty", "warning");
    }
    $career_points=se($info, "career_points", "-1", false);
    if(empty($career_points) && $career_points<-1){
        flash("Career Points must not be empty", "warning");
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
    ["type" => "number", "name" => "highest_race_finish", "placeholder" => "Wins", "label" => "Wins", "rules" => ["required" => "required"]],
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
    <form method="POST" onsubmit="return validate(this);">
        <?php foreach($form as $k=>$v){
            render_input($v);
        } ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text"=> "Update"]); ?>
    </form>
</div> 

<script>
    function validate(form){
        let isValid=true;
        
        let name=form.name.value;
        if(name===""){
            flash("Name must not be empty [js]", "warning");
            isValid=false;
        }
        let abbr=form.abbr.value;
        if(abbr===""){
            flash("Abbr must not be empty [js]", "warning");
            isValid=false;
        }
        let image=form.image.value;
        if(image===""){
            flash("Image must not be empty [js]", "warning");
            isValid=false;
        }
        let nationality=form.nationality.value;
        if(nationality===""){
            flash("Nationality must not be empty [js]", "warning");
            isValid=false;
        }
        let country=form.country.value;
        if(country===""){
            flash("Country must not be empty [js]", "warning");
            isValid=false;
        }
        let birthdate=form.birthdate.value;
        if(birthdate===""){
            flash("Birthdate must not be empty [js]", "warning");
            isValid=false;
        }
        let birthplace=form.birthplace.value;
        if(birthplace===""){
            flash("Birthplace must not be empty [js]", "warning");
            isValid=false;
        }
        let number=form.number.value;
        if(number===""){
            flash("Number must not be empty [js]", "warning");
            isValid=false;
        }
        let grands_prix_entered=form.grands_prix_entered.value;
        if(grands_prix_entered===""){
            flash("GPs Entered must not be empty [js]", "warning");
            isValid=false;
        }
        let world_championships=form.world_championships.value;
        if(world_championships===""){
            flash("World Championships must not be empty [js]", "warning");
            isValid=false;
        }
        let podiums=form.podiums.value;
        if(podiums===""){
            flash("Podiums must not be empty [js]", "warning");
            isValid=false;
        }
        let highest_race_finish=form.highest_race_finish.value;
        if(highest_race_finish===""){
            flash("Highest race finish must not be empty [js]", "warning");
            isValid=false;
        }
        let highest_grid_position=form.highest_grid_position.value;
        if(highest_grid_position===""){
            flash("Highest Grid Position must not be empty [js]", "warning");
            isValid=false;
        }
        let career_points=form.career_points.value;
        if(career_points===""){
            flash("Career Points must not be empty [js]", "warning");
            isValid=false;
        }
        return isValid;

    }

</script>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>