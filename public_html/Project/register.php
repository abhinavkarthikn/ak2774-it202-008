<?php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
?>
<div class="container-fluid">
    <form onsubmit="return validate(this)" method="POST">
        <?php render_input(["type" => "email", "id" => "email", "name" => "email", "label" => "Email", "rules" => ["required" => true]]); ?>
        <?php render_input(["type" => "text", "id" => "username", "name" => "username", "label" => "Username", "rules" => ["required" => true, "maxlength" => 30]]); ?>
        <?php render_input(["type" => "password", "id" => "password", "name" => "password", "label" => "Password", "rules" => ["required" => true, "minlength" => 8]]); ?>
        <?php render_input(["type" => "password", "id" => "confirm", "name" => "confirm", "label" => "Confirm Password", "rules" => ["required" => true, "minlength" => 8]]); ?>
        <?php render_button(["text" => "Register", "type" => "submit"]); ?>
    </form>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success
        var email = form.email.value;
        var username = form.username.value;
        var password = form.password.value;
        var confirm = form.confirm.value;
        let isValid = true;
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var usernamePattern = /^[a-zA-Z0-9_-]{3,16}$/;


        if (email === "") {
            flash("Email must not be empty [js]", "danger");
            isValid = false;
        } else if (!emailPattern.test(email)) {
            flash("Invalid email address [js]", "danger"); //ak2774
            isValid = false; //4/1/2024
        }
        if (username === "") {
            flash("Username must not be empty [js]", "danger");
            isValid = false;
        } else if (!usernamePattern.test(username)) {
            flash("Username must only contain 3-16 characters a-z, 0-9, _, or - [js]", "danger");
            isValid = false;
        }
        if (password === "") {
            flash("Password must not be empty [js]", "danger");
            isValid = false;
        } else if (password.length < 8 || confirm.length < 8) {
            flash("Password must be at least 8 characters long [js]", "danger");
            isValid = false;
        }
        if (password != confirm) {
            flash("Passwords do not match [js]", "danger");
            isValid = false;
        }

        return isValid;



        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["username"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("Username must only contain 3-16 characters a-z, 0-9, _, or -", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>