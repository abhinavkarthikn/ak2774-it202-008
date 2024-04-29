<?php
// Note: we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["users"]) && isset($_POST["drivers"])) {
        $user_ids = $_POST["users"];
        $driver_ids = $_POST["drivers"];
        
        if (empty($user_ids) || empty($driver_ids)) {
            flash("Both users and drivers need to be selected", "warning");  //ak2774, 4/29/24
        } else {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO UserDrivers (user_id, driver_id, is_active) VALUES (:uid, :did, 1) 
                ON DUPLICATE KEY UPDATE is_active = IF(is_active = 1, 0, 1)");
            foreach ($driver_ids as $did) {
                foreach ($user_ids as $uid) {
                    try {
                        $stmt->execute([":uid" => $uid, ":did" => $did]);
                        flash("Assigned driver to user", "success");
                    } catch (PDOException $e) {
                        flash(var_export($e->errorInfo, true), "danger");
                    }
                }
            }
        }
    } 
}

// Initialize variables
$drivers = [];
$driver_name = "";

    if (isset($_POST["driver_name"])) {
        $driver_name = se($_POST, "driver_name", "", false);
        if(!empty($driver_name)){
            $db=getDB();
            $stmt = $db->prepare("SELECT id, name FROM Drivers WHERE name LIKE :driver_name LIMIT 25");
            try{
                $stmt->execute([":driver_name" => "%$driver_name%"]);
                $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
                if($results){
                    $drivers=$results;
                }
            }
            catch(PDOException $e){
                flash(var_export($e->errorInfo, true), "danger");  //ak2774, 4/29/24
            }
        }
     else {
        flash("Driver name must not be empty", "warning");
    }
}

$users=[];
$username="";
if(isset($_POST["username"])){
    $username=se($_POST, "username", "", false);
    if(!empty($username)){
        $db=getDB();
        $stmt=$db->prepare("SELECT id, username FROM Users WHERE username LIKE :username LIMIT 25");
        try{
            $stmt->execute([":username"=>"%$username%"]);
            $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if($results){
                $users=$results;
            }
        }
        catch(PDOException $e){
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
    else{
        flash("Username must not be empty", "warning");
    }
}
?>

<div class="container-fluid">
    <h1>Associate Users to Drivers</h1>
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "username", "placeholder" => "Username Search", "value" => $username]); ?>
        <?php render_input(["type" => "search", "name" => "driver_name", "placeholder" => "Driver Name Search", "value" => $driver_name]); ?>
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
    </form>

    <form method="POST">
        <?php if (!empty($username) && !empty($driver_name)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
            <input type="hidden" name="driver_name" value="<?php se($driver_name, false); ?>" />

            <table class="table">
                <thead>
                    <th>Users</th>
                    <th>Drivers to Assign</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="table">
                                <?php foreach ($users as $user) :   //ak2774, 4/29/24?>
                                    <tr>
                                        <td>
                                            <?php render_input(["type" => "checkbox", "id" => "user_" . se($user, 'id', "", false), 
                                            "name" => "users[]", "label" => se($user, "username", "", false), "value" => se($user, 'id', "", false)]); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                        <td>
                            <?php if (!empty($drivers)) : ?>
                                <table class="table">
                                    <?php foreach ($drivers as $driver) : ?>
                                        <tr>
                                            <td>
                                                <?php render_input(["type" => "checkbox", "id" => "driver_" . se($driver, 'id', "", false), 
                                                "name" => "drivers[]", "label" => se($driver, "name", "", false), "value" => se($driver, 'id', "", false)]); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php render_button(["text" => "Assign Drivers", "type" => "submit", "color" => "secondary"]);  //ak2774, 4/29/24?>
        <?php endif; ?>
    </form>
</div>

<?php
// Include flash messages
require_once(__DIR__ . "/../../../partials/flash.php");
?>
