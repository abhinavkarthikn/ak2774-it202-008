<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

$form = [
    ["type" => "text", "name" => "username", "placeholder" => "Username", "label" => "Username", "include_margin" => false],
    ["type" => "text", "name" => "name", "placeholder" => "Name", "label" => "Name", "include_margin" => false],
    ["type" => "text", "name" => "country", "placeholder" => "Country", "label" => "Country", "include_margin" => false],
    ["type" => "number", "name" => "number", "placeholder" => "Driver #", "label" => "Driver #", "include_margin" => false],

    ["type" => "number", "name" => "grands_prix_entered_min", "placeholder" => "Min GPs", "label" => "Min GPs", "include_margin" => false],
    ["type" => "number", "name" => "grands_prix_entered_max", "placeholder" => "Max GPs", "label" => "Max GPs", "include_margin" => false],

    ["type" => "number", "name" => "world_championships_min", "placeholder" => "Min WCs", "label" => "Min WCs", "include_margin" => false],
    ["type" => "number", "name" => "world_championships_max", "placeholder" => "Max WCs", "label" => "Max WCs", "include_margin" => false],

    ["type" => "number", "name" => "highest_race_finish_min", "placeholder" => "Min Wins", "label" => "Min Wins", "include_margin" => false],
    ["type" => "number", "name" => "highest_race_finish_max", "placeholder" => "Max Wins", "label" => "Max Wins", "include_margin" => false],

    ["type" => "number", "name" => "podiums_min", "placeholder" => "Min Podiums", "label" => "Min Podiums", "include_margin" => false],
    ["type" => "number", "name" => "podiums_max", "placeholder" => "Max Podiums", "label" => "Max Podiums", "include_margin" => false],

    ["type" => "number", "name" => "career_points_min", "placeholder" => "Min Points", "label" => "Min Points", "include_margin" => false],
    ["type" => "number", "name" => "career_points_max", "placeholder" => "Max Points", "label" => "Max Points", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => [
        "grands_prix_entered" => "GPs", "world_championships" => "WCs",
        "highest_race_finish" => "Wins", "podiums" => "Podiums", "career_points" => "Points"
    ], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false]
];

$total_records = get_total_count("`Drivers` d JOIN `UserDrivers` ud ON d.id=ud.driver_id");

/*$query = "SELECT u.username, d.id, name, abbr, image, country, birthdate, number, grands_prix_entered, world_championships, podiums, highest_race_finish, career_points, ud.user_id FROM `Drivers` d 
JOIN `UserDrivers` ud ON d.id=ud.driver_id JOIN Users u on u.id=ud.user_id WHERE ud.is_active=1";*/

$query="SELECT 
(SELECT u.username FROM Users u WHERE u.id=udr.user_id LIMIT 1) AS username, d.id, name, abbr, image, country, birthdate, number, grands_prix_entered, world_championships, podiums, highest_race_finish, career_points, udr.user_id,
(SELECT COUNT(ud.user_id) FROM `UserDrivers` ud WHERE ud.driver_id=d.id) AS total_users
FROM `Drivers` d LEFT JOIN `UserDrivers` udr ON d.id=udr.driver_id JOIN Users us on us.id=udr.user_id
WHERE udr.is_active=1";

$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    redirect($session_key);
} else {
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

    $queryConditions = [];
    $params = [];

    $username = se($_GET, "username", "", false);
    if (!empty($username)) {
        $queryConditions[] = "username LIKE :username";
        $params[":username"] = "%$username%";
    }

    $name = se($_GET, "name", "", false);
    if (!empty($name)) {
        $queryConditions[] = "name LIKE :name";
        $params[":name"] = "%$name%";
    }

    $country = se($_GET, "country", "", false);
    if (!empty($country)) {
        $queryConditions[] = "country LIKE :country";
        $params[":country"] = "%$country%";
    }

    // Add other filter conditions similarly...

    if (!empty($queryConditions)) {
        $query .= " AND " . implode(" AND ", $queryConditions);
    }

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
    error_log("Error fetching drivers: " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}
foreach ($results as $index => $driver) {
    foreach ($driver as $key => $value) {
        if (is_null($value)) {
            $results[$index][$key] = "N/A";
        }
    }
}

$table = [
    "data" => $results, "title" => "Drivers", "ignored_columns" => ["id"],
    "view_url" => get_url("driver.php"),
];

$remove = !empty($username) || !empty($name) || !empty($country) || !empty($_GET["number"]) || !empty($_GET["grands_prix_entered_min"]) || !empty($_GET["grands_prix_entered_max"]) || !empty($_GET["world_championships_min"]) || !empty($_GET["world_championships_max"]) || !empty($_GET["highest_race_finish_min"]) || !empty($_GET["highest_race_finish_max"]) || !empty($_GET["podiums_min"]) || !empty($_GET["podiums_max"]) || !empty($_GET["career_points_min"]) || !empty($_GET["career_points_max"]);

require(__DIR__ . "/../../../partials/flash.php");
?>

<div class="container-fluid">
    <h3>Associated Drivers</h3>
    <div>
        <?php if ($remove) : ?>
            <a class="btn btn-danger" href="<?php echo get_url('remove_all.php?' . http_build_query($_GET)); ?>" class="card-link">Remove All</a>
        <?php endif; ?>
    </div>
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
    <?php render_result_counts(count($results), $total_records); ?>
    <div class="row w-100 row-cols-auto row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
        <?php foreach ($results as $driver) : ?>
            <div class="col">
                <?php render_driver_card($driver); ?>
            </div>
        <?php endforeach; ?>
        <?php if (count($results) === 0) : ?>
            <div class="col">
                No results to show
            </div>
        <?php endif; ?>
    </div>
</div>
