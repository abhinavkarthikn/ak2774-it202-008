<?php
require_once(__DIR__ . "/../../partials/nav.php");
$user_id = -1;
try {
    $user_id = (int)se($_GET, "id", -1, false);
} catch (Exception $e) {
}

if ($user_id < 1) {
    $user_id = get_user_id();
}

$is_me = $user_id == get_user_id();
$is_edit = isset($_GET["edit"]);
?>
<?php
if ($is_me && $is_edit && isset($_POST["save"])) {
    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);

    $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
    try {
        $stmt->execute($params);
        flash("Profile saved", "success");
    } catch (Exception $e) {
        if ($e->errorInfo[1] === 1062) {
            //https://www.php.net/manual/en/function.preg-match.php
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                flash("The chosen " . $matches[1] . " is not available.", "warning");
            } else {
                //TODO come up with a nice error message
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            //TODO come up with a nice error message
            echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
        }
    }
    //select fresh data from table
    $stmt = $db->prepare("SELECT id, email, username from Users where id = :id LIMIT 1");
    try {
        $stmt->execute([":id" => get_user_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            //$_SESSION["user"] = $user;
            $_SESSION["user"]["email"] = $user["email"];
            $_SESSION["user"]["username"] = $user["username"];
        } else {
            flash("User doesn't exist", "danger");
        }
    } catch (Exception $e) {
        flash("An unexpected error occurred, please try again", "danger");
        //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            //TODO validate current
            $stmt = $db->prepare("SELECT password from Users where id = :id");
            try {
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result["password"])) {
                    if (password_verify($current_password, $result["password"])) {
                        $query = "UPDATE Users set password = :password where id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ":id" => get_user_id(),
                            ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                        ]);

                        flash("Password reset", "success");
                    } else {
                        flash("Current password is invalid", "warning");
                    }
                }
            } catch (Exception $e) {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            flash("New passwords don't match", "warning");
        }
    }
}
?>

<?php
$user = [];
if ($user_id > 0) {
    $db = getDB();
    $query = "SELECT email, username, created FROM Users where id=:user_id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id" => $user_id]);
        $r = $stmt->fetch();
        if ($r) {
            $user = $r;
        } else {
            flash("Couldn't find user profile", "warning");
        }
    } catch (PDOException $e) {
        error_log("Error fetching user:" . var_export($e, true));
        flash("Error fetching user", "danger");
    }
}
?>
<div class="container-fluid">
    <?php if ($is_me && $is_edit) : ?>
        <a class="btn btn-secondary btn-sm" href="?">View</a>
        <form method="POST" onsubmit="return validate(this);">
            <?php render_input(["type" => "email", "id" => "email", "name" => "email", "label" => "Email", "value" => se($user, "email", "", false), "rules" => ["required" => true]]); ?>
            <?php render_input(["type" => "text", "id" => "username", "name" => "username", "label" => "Username", "value" => se($user, "username", "", false), "rules" => ["required" => true, "maxlength" => 30]]); ?>
            <!-- DO NOT PRELOAD PASSWORD -->
            <div class="lead">Password Reset</div>
            <?php render_input(["type" => "password", "id" => "cp", "name" => "currentPassword", "label" => "Current Password", "rules" => ["minlength" => 8]]); ?>
            <?php render_input(["type" => "password", "id" => "np", "name" => "newPassword", "label" => "New Password", "rules" => ["minlength" => 8]]); ?>
            <?php render_input(["type" => "password", "id" => "conp", "name" => "confirmPassword", "label" => "Confirm Password", "rules" => ["minlength" => 8]]); ?>
            <?php render_input(["type" => "hidden", "name" => "save"]);/*lazy value to check if form submitted, not ideal*/ ?>
            <?php render_button(["text" => "Update Profile", "type" => "submit"]); ?>
        </form>
    <?php else : ?>
        <?php if ($is_me) : ?>
            <a class="btn btn-secondary btn-sm" href="?edit">Edit</a>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <div class="h4">Username: <?php se($user, "username"); ?></div>
                <div class="text-body">Joined: <?php se($user, "created"); ?></div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function validate(form) {
        let email = form.email.value;
        let username = form.username.value;
        let pwd = form.currentPassword.value;
        let pw = form.newPassword.value;
        let con = form.confirmPassword.value;
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var usernamePattern = /^[a-zA-Z0-9_-]{3,16}$/;
        let isValid = true;
        //TODO add other client side validation....

        //example of using flash via javascript
        //find the flash container, create a new element, appendChild

        if (email === "") {
            flash("Email must not be empty [js]", "warning");
            isValid = false;
        } else if (!emailPattern.test(email)) {
            flash("Invalid email address [js]", "warning");
            isValid = false;
        }

        if (username === "") {
            flash("Username must not be empty [js]", "warning");
            isValid = false;
        } else if (!usernamePattern.test(username)) {
            flash("Username must only contain 3-16 characters a-z, 0-9, _, or - [js]", "warning");
            isValid = false;
        }

        if (pwd !== "") {
            if (pw === "") {
                flash("New password must not be empty [js]", "warning");
                isValid = false;
            }

            // Check if new password meets minimum length requirement
            else if (pw.length < 8) {
                flash("New password must be at least 8 characters long [js]", "warning");
                isValid = false;
            }

            // Check if confirm password is empty
            if (con === "") {
                flash("Confirm password must not be empty [js]", "warning");
                isValid = false;
            }

            if (pw !== con) {
                flash("Password and Confirm password must match [js]", "warning");
                isValid = false;
            }
        }


        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>