<?php

require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to view this page", "warning"); 
    redirect("home.php");
}

//build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Name", "label" => "Name", "include_margin" => false],
    ["type" => "text", "name" => "base", "placeholder" => "Base", "label" => "Base", "include_margin" => false],

    ["type" => "number", "name" => "pole_positions_min", "placeholder" => "Min Poles", "label" => "Min Poles", "include_margin" => false],
    ["type" => "number", "name" => "pole_positions_max", "placeholder" => "Max Poles", "label" => "Max Poles", "include_margin" => false],

    ["type" => "number", "name" => "world_championships_min", "placeholder" => "Min WCs", "label" => "Min WCs", "include_margin" => false],
    ["type" => "number", "name" => "world_championships_max", "placeholder" => "Max WCs", "label" => "Max WCs", "include_margin" => false],

    ["type" => "number", "name" => "highest_race_finish_min", "placeholder" => "Min Wins", "label" => "Min Wins", "include_margin" => false],
    ["type" => "number", "name" => "highest_race_finish_max", "placeholder" => "Max Wins", "label" => "Max Wins", "include_margin" => false],

    ["type" => "number", "name" => "fastest_laps_min", "placeholder" => "Min Fastest Laps", "label" => "Min Fastest Laps", "include_margin" => false],  
    ["type" => "number", "name" => "fastest_laps_max", "placeholder" => "Max Fastest Laps", "label" => "Max Fastest Laps", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["pole_positions" => "Poles", "world_championships" => "WCs", 
    "highest_race_finish" => "Wins", "fastest_laps" => "Fastest Laps"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false]


];

$total_records=get_total_count("`Teams`");

$query= "SELECT name, base, world_championships, highest_race_finish, pole_positions, fastest_laps FROM `Teams` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear=isset($_GET["clear"]);
if($is_clear){
    session_delete($session_key);
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
    $base = se($_GET, "base", "", false);
    if (!empty($base)) {
        $query .= " AND base LIKE :base";   
        $params[":base"] = "%$base%";
    }
    //pole range
    $pole_positions_min = se($_GET, "pole_positions_min", "-1", false);
    if (!empty($pole_positions_min) && $pole_positions_min > -1) {
        $query .= " AND pole_positions >= :pole_positions_min";
        $params[":pole_positions_min"] = $pole_positions_min;
    }
    $pole_positions_max = se($_GET, "pole_positions_max", "-1", false);
    if (!empty($pole_positions_max) && $pole_positions_max > -1) {
        $query .= " AND pole_positions <= :pole_positions_max";
        $params[":pole_positions_max"] = $pole_positions_max;
    }
    if($pole_positions_min > $pole_positions_max && !empty($pole_positions_min) && !empty($pole_positions_max)){
        flash("Min Poles must be less than Max Poles", "warning");
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
    $highest_race_finish_max= se($_GET, "highest_race_finish_max", "-1", false);
    if(!empty($highest_race_finish_max) && $highest_race_finish_max > -1){
        $query .= " AND highest_race_finish <= :highest_race_finish_max";
        $params[":highest_race_finish_max"] = $highest_race_finish_max;
    }
    if($highest_race_finish_min > $highest_race_finish_max && !empty($highest_race_finish_min) && !empty($highest_race_finish_max)){
        flash("Min Wins must be less than Max Wins", "warning");
    }
    
    //fastest laps range
    $fastest_laps_min = se($_GET, "fastest_laps_min", "-1", false);
    if (!empty($fastest_laps_min) && $fastest_laps_min > -1) {
        $query .= " AND fastest_laps >= :fastest_laps_min";
        $params[":fastest_laps_min"] = $fastest_laps_min;
    }
    $fastest_laps_max = se($_GET, "fastest_laps_max", "-1", false);
    if (!empty($fastest_laps_max) && $fastest_laps_max > -1) {
        $query .= " AND fastest_laps <= :fastest_laps_max";
        $params[":fastest_laps_max"] = $fastest_laps_max;
    }
    if($fastest_laps_min > $fastest_laps_max && !empty($fastest_laps_min) && !empty($fastest_laps_max)){  
        flash("Min Fastest Laps must be less than Max Fastest Laps", "warning");
    }

    //sort and order
    $sort = se($_GET, "sort", "world_championships", false);
    if (!in_array($sort, ["pole_positions", "world_championships", "highest_race_finish", "fastest_laps"])) {
        $sort = "world_championships";
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
    error_log("Error fetching teams " . var_export($e, true));
    flash("Unhandled error occured ", "danger");
}

$table = ["data" => $results, "title" => "All Drivers", "ignored_columns" => ["id"], 
//"edit_url" => get_url("admin/edit_driver.php"),
//"delete_url" => get_url("admin/delete_driver.php"),
//"view_url" => get_url("admin/view_driver.php")
];
?>

<div class="container-fluid">
    <h3>List Teams</h3>
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
    <?php render_result_counts(count($results), $total_records); ?>
    <?php render_table($table); ?>
</div>

<script>
    function validate(form){
        let isValid=true;
        
        let minPoles = parseInt(form.pole_positions_min.value);
        let maxPoles = parseInt(form.pole_positions_max.value);
        if(minPoles > maxPoles && minPoles!="" && maxPoles!=""){
            flash("Min Poles must be less than Max Poles [js]", "warning");
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

        let minLaps = parseInt(form.fastest_laps_min.value);
        let maxLaps = parseInt(form.fastest_laps_max.value);
        if(minLaps > maxLaps && minLaps!="" && maxLaps!=""){
            flash("Min Fastest Laps must be less than Max Fastest Laps [js]", "warning");  
            isValid=false;
        }
        return isValid;
    }

    
    </script>





<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>