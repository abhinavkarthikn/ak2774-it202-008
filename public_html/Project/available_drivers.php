<?php

require(__DIR__ . "/../../partials/nav.php");



//build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Name", "label" => "Name", "include_margin" => false],
    ["type" => "text", "name" => "country", "placeholder" => "Country", "label" => "Country", "include_margin" => false],
    ["type" => "number", "name" => "number", "placeholder" => "Driver #", "label" => "Driver #", "include_margin" => false],

    ["type" => "number", "name" => "grands_prix_entered_min", "placeholder" => "Min GPs", "label" => "Min GPs", "include_margin" => false],
    ["type" => "number", "name" => "grands_prix_entered_max", "placeholder" => "Max GPs", "label" => "Max GPs", "include_margin" => false],

    ["type" => "number", "name" => "world_championships_min", "placeholder" => "Min WCs", "label" => "Min WCs", "include_margin" => false],
    ["type" => "number", "name" => "world_championships_max", "placeholder" => "Max WCs", "label" => "Max WCs", "include_margin" => false],

    ["type" => "number", "name" => "highest_race_finish_min", "placeholder" => "Min Wins", "label" => "Min Wins", "include_margin" => false],
    ["type" => "number", "name" => "highest_race_finish_max", "placeholder" => "Max Wins", "label" => "Max Wins", "include_margin" => false],

    ["type" => "number", "name" => "podiums_min", "placeholder" => "Min Podiums", "label" => "Min Podiums", "include_margin" => false],  //ak2774, 4/29/24
    ["type" => "number", "name" => "podiums_max", "placeholder" => "Max Podiums", "label" => "Max Podiums", "include_margin" => false],

    ["type" => "number", "name" => "career_points_min", "placeholder" => "Min Points", "label" => "Min Points", "include_margin" => false],
    ["type" => "number", "name" => "career_points_max", "placeholder" => "Max Points", "label" => "Max Points", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["grands_prix_entered" => "GPs", "world_championships" => "WCs", 
    "highest_race_finish" => "Wins", "podiums" => "Podiums", "career_points" => "Points"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false]


];

$total_records=get_total_count("`Drivers` d WHERE d.id NOT IN (SELECT driver_id FROM `UserDrivers`)");


$query = "SELECT d.id, name, image, abbr, country, birthdate, number, grands_prix_entered, world_championships, podiums, highest_race_finish, 
career_points FROM `Drivers` d
WHERE d.id NOT IN (SELECT driver_id FROM `UserDrivers`) 
UNION 
SELECT c.id, name, image, abbr, country, birthdate, number, grands_prix_entered, world_championships, podiums, highest_race_finish, career_points FROM `Drivers` c 
JOIN `UserDrivers` ud ON c.id=ud.driver_id WHERE ud.is_active=0";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear=isset($_GET["clear"]);
if($is_clear){
    session_delete($session_key);    //ak2774, 4/29/24
    unset($_GET["clear"]);
    redirect($session_key);
}
else{
    $session_data = session_load($session_key);     
}

if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) {
    if ($session_data) {
        $_GET = $session_data;
    }
}



    session_save($session_key, $_GET);
    $keys = array_keys($_GET);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }
    $name = se($_GET, "name", "", false);
    if (!empty($name)) {
        $query .= " AND name LIKE :name";
        $params[":name"] = "%$name%";
    }
    $nationality = se($_GET, "nationality", "", false);
    if (!empty($nationality)) {
        $query .= " AND nationality LIKE :nationality";   
        $params[":nationality"] = "%$nationality%";
    }
    $country = se($_GET, "country", "", false);
    if (!empty($country)) {
        $query .= " AND country LIKE :country";
        $params[":country"] = "%$country%";
    }
    $number = se($_GET, "number", "-1", false);
    if (!empty($number) && $number > -1) {
        $query .= " AND number = :number";
        $params[":number"] = $number;
    }
    //gp's range
    $grands_prix_entered_min = se($_GET, "grands_prix_entered_min", "-1", false);
    if (!empty($grands_prix_entered_min) && $grands_prix_entered_min > -1) {
        $query .= " AND grands_prix_entered >= :grands_prix_entered_min";
        $params[":grands_prix_entered_min"] = $grands_prix_entered_min;
    }
    $grands_prix_entered_max = se($_GET, "grands_prix_entered_max", "-1", false);   //ak2774, 4/29/24
    if (!empty($grands_prix_entered_max) && $grands_prix_entered_max > -1) {
        $query .= " AND grands_prix_entered <= :grands_prix_entered_max";
        $params[":grands_prix_entered_max"] = $grands_prix_entered_max;
    }
    if($grands_prix_entered_min > $grands_prix_entered_max && !empty($grands_prix_entered_min) && !empty($grands_prix_entered_max)){
        flash("Min GPs must be less than Max GPs", "warning");
    }

    //wc's range
    $world_championships_min = se($_GET, "world_championships_min", "-1", false);
    if (!empty($world_championships_min) && $world_championships_min > -1) {
        $query .= " AND world_championships >= :world_championships_min";
        $params[":world_championships_min"] = $world_championships_min;
    }
    $world_championships_max = se($_GET, "world_championships_max", "-1", false);   
    if (!empty($world_championships_max) && $world_championships_max > -1) {
        $query .= " AND world_championships <= :world_championships_max";
        $params[":world_championships_max"] = $world_championships_max;
    }
    if($world_championships_min > $world_championships_max && !empty($world_championships_min) && !empty($world_championships_max)){
        flash("Min WCs must be less than Max WCs", "warning");
    }

    //wins range
    $highest_race_finish_min= se($_GET, "highest_race_finish_min", "-1", false);
    if(!empty($highest_race_finish_min) && $highest_race_finish_min > -1){
        $query .= " AND highest_race_finish >= :highest_race_finish_min";
        $params[":highest_race_finish_min"] = $highest_race_finish_min;
    }
    $highest_race_finish_max= se($_GET, "highest_race_finish_max", "-1", false);    //ak2774, 4/29/24
    if(!empty($highest_race_finish_max) && $highest_race_finish_max > -1){
        $query .= " AND highest_race_finish <= :highest_race_finish_max";
        $params[":highest_race_finish_max"] = $highest_race_finish_max;
    }
    if($highest_race_finish_min > $highest_race_finish_max && !empty($highest_race_finish_min) && !empty($highest_race_finish_max)){
        flash("Min Wins must be less than Max Wins", "warning");
    }
    
    //podiums range
    $podiums_min = se($_GET, "podiums_min", "-1", false);
    if (!empty($podiums_min) && $podiums_min > -1) {
        $query .= " AND podiums >= :podiums_min";
        $params[":podiums_min"] = $podiums_min;
    }
    $podiums_max = se($_GET, "podiums_max", "-1", false);
    if (!empty($podiums_max) && $podiums_max > -1) {
        $query .= " AND podiums <= :podiums_max";
        $params[":podiums_max"] = $podiums_max;
    }
    if($podiums_min > $podiums_max && !empty($podiums_min) && !empty($podiums_max)){ 
        flash("Min Podiums must be less than Max Podiums", "warning");
    }

    //points range
    $career_points_min = se($_GET, "career_points_min", "-1", false);
    if (!empty($career_points_min) && $career_points_min > -1) {
        $query .= " AND career_points >= :career_points_min";
        $params[":career_points_min"] = $career_points_min;
    }
    $career_points_max = se($_GET, "career_points_max", "-1", false);     //ak2774, 4/29/24
    if (!empty($career_points_max) && $career_points_max > -1) {
        $query .= " AND career_points <= :career_points_max";
        $params[":career_points_max"] = $career_points_max;
    }
    if($career_points_min > $career_points_max && !empty($career_points_min) && !empty($career_points_max)){ 
        flash("Min Points must be less than Max Points", "warning");
    }

    //sort and order
    $sort = se($_GET, "sort", "grands_prix_entered", false);
    if (!in_array($sort, ["grands_prix_entered", "world_championships", "highest_race_finish", "podiums", "career_points"])) {
        $sort = "grands_prix_entered";
    }
    $order = se($_GET, "order", "desc", false);  
    if (!in_array($order, ["asc", "desc"])) {
        $order = "desc";
    }

    $query .= " ORDER BY $sort $order";

    try {
        $limit = (int)se($_GET, "limit", "10", false);
    } catch (Exception $e) {
        $limit = 10;
    }
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }

    $query .= " LIMIT $limit";





$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();  
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching drivers " . var_export($e, true));   //ak2774, 4/29/24
    flash("Unhandled error occured ", "danger");
}

$table = ["data" => $results, "title" => "All Drivers", "ignored_columns" => ["id"], 
"view_url" => get_url("driver.php")
];
?>

<div class="container-fluid">
    <h3>Available Drivers</h3>
    <form method="GET" onsubmit="return validate(this);">
        <div class="row mb-3" style="align-items: flex-end;">

            <?php foreach ($form as $k => $v) : ?>
                <div class="col col-2">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>

        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_result_counts(count($results), $total_records);  //ak2774, 4/29/24 ?>
    <div class="row w-100 row-cols-auto row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
        <?php foreach ($results as $driver) : ?>
            <div class="col">
                <?php render_driver_card($driver); ?>
            </div>
        <?php endforeach; ?>
        <?php if(count($results)===0): ?>
            <div class="col">
                No results to show
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function validate(form){
        let isValid=true;
        
        let minGPs = parseInt(form.grands_prix_entered_min.value);
        let maxGPs = parseInt(form.grands_prix_entered_max.value);
        if(minGPs > maxGPs && minGPs!="" && maxGPs!=""){
            flash("Min GPs must be less than Max GPs [js]", "warning");
            isValid=false;
        }
        let minWCs = parseInt(form.world_championships_min.value);
        let maxWCs = parseInt(form.world_championships_max.value);
        if(minWCs > maxWCs && minWCs!="" && maxWCs!=""){
            flash("Min WCs must be less than Max WCs [js]", "warning");
            isValid=false;
        }

        let minWins=parseInt(form.highest_race_finish_min.value);
        let maxWins=parseInt(form.highest_race_finish_max.value);
        if(minWins > maxWins && minWins!="" && maxWins!=""){
            flash("Min Wins must be less than Max Wins [js]", "warning");
            isValid=false;
        }

        let minPodiums = parseInt(form.podiums_min.value);
        let maxPodiums = parseInt(form.podiums_max.value);
        if(minPodiums > maxPodiums && minPodiums!="" && maxPodiums!=""){
            flash("Min Podiums must be less than Max Podiums [js]", "warning");
            isValid=false;
        }
        let minPoints = parseInt(form.career_points_min.value);
        let maxPoints = parseInt(form.career_points_max.value);
        if(minPoints > maxPoints && minPoints!="" && maxPoints!=""){
            flash("Min Points must be less than Max Points [js]", "warning");
            isValid=false;
        }
        return isValid;
    }

    
    </script>





<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>