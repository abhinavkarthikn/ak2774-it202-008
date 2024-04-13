<?php

require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You do not have permission to view this page", "warning");
    die(header("location: $BASE_PATH" . "/home.php"));
}

//build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Name", "label" => "Name", "include_margin" => false],
    ["type" => "text", "name" => "nationality", "placeholder" => "Nationality", "label" => "Nationality", "include_margin" => false],
    ["type" => "text", "name" => "country", "placeholder" => "Country", "label" => "Country", "include_margin" => false],
    ["type" => "number", "name" => "number", "placeholder" => "Driver #", "label" => "Driver #", "include_margin" => false],

    ["type" => "number", "name" => "grands_prix_entered_min", "placeholder" => "Min GPs", "label" => "Min GPs", "include_margin" => false],
    ["type" => "number", "name" => "grands_prix_entered_max", "placeholder" => "Max GPs", "label" => "Max GPs", "include_margin" => false],

    ["type" => "number", "name" => "world_championships_min", "placeholder" => "Min WCs", "label" => "Min WCs", "include_margin" => false],
    ["type" => "number", "name" => "world_championships_max", "placeholder" => "Max WCs", "label" => "Max WCs", "include_margin" => false],

    ["type" => "number", "name" => "podiums_min", "placeholder" => "Min Podiums", "label" => "Min Podiums", "include_margin" => false],
    ["type" => "number", "name" => "podiums_max", "placeholder" => "Max Podiums", "label" => "Max Podiums", "include_margin" => false],

    ["type" => "number", "name" => "career_points_min", "placeholder" => "Min Points", "label" => "Min Points", "include_margin" => false],
    ["type" => "number", "name" => "career_points_max", "placeholder" => "Max Points", "label" => "Max Points", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["grands_prix_entered" => "GPs", "world_championships" => "WCs", "podiums" => "Podiums", "career_points" => "Points"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false]


];





$query = "SELECT id, name, abbr, image, nationality, country, birthdate, birthplace, number, grands_prix_entered, world_championships, podiums, highest_race_finish, highest_grid_position, career_points FROM `Drivers` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear=isset($_GET["clear"]);
if($is_clear){
    session_delete($session_key);
    unset($_GET["clear"]);
    die(header("Location: " . $session_key));
}
else{
    $session_data = session_load($session_key);
}

if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) {
    if ($session_data) {
        $_GET = $session_data;
    }
}


if (count($_GET) > 0) {
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
    $grands_prix_entered_max = se($_GET, "grands_prix_entered_max", "-1", false);
    if (!empty($grands_prix_entered_max) && $grands_prix_entered_max > -1) {
        $query .= " AND grands_prix_entered <= :grands_prix_entered_max";
        $params[":grands_prix_entered_max"] = $grands_prix_entered_max;
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
    //points range
    $career_points_min = se($_GET, "career_points_min", "-1", false);
    if (!empty($career_points_min) && $career_points_min > -1) {
        $query .= " AND career_points >= :career_points_min";
        $params[":career_points_min"] = $career_points_min;
    }
    $career_points_max = se($_GET, "career_points_max", "-1", false);
    if (!empty($career_points_max) && $career_points_max > -1) {
        $query .= " AND career_points <= :career_points_max";
        $params[":career_points_max"] = $career_points_max;
    }
    //sort and order
    $sort = se($_GET, "sort", "grands_prix_entered", false);
    if (!in_array($sort, ["grands_prix_entered", "world_championships", "podiums", "career_points"])) {
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
}




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
    error_log("Error detching drivers " . var_export($e, true));
    flash("Unhandled error occured ", "danger");
}

$table = ["data" => $results, "title" => "All Drivers", "ignored_columns" => ["id"], 
"edit_url" => get_url("admin/edit_driver.php"),
"delete_url" => get_url("admin/delete_driver.php")];
?>

<div class="container-fluid">
    <h3>List Drivers</h3>
    <form method="GET">
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
    <?php render_table($table); ?>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>