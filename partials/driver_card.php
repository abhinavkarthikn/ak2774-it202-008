<?php
if (!isset($driver)) {
    error_log("Using Driver partial without data");
    flash("Dev Alert: Driver called without data", "danger");
}
?>

<?php if(isset($driver)) : ?>
    <div class="card" style="width: 18rem;">
        <?php if(isset($driver["username"])) : ?>
            <div class="card-header">
            Favorited By: <a href="<?php echo get_url("profile.php?id=" .$driver["user_id"]); ?>"><?php se($driver, "username", "N/A"); ?></a>
            </div>
        <?php endif; ?>
        <?php if(!empty($driver["image"])):?>
            <img src="<?php echo $driver["image"];?>" class="card-img-top" alt="Driver Image">
        <?php endif;?>

        <div class="card-body">
            <h5 class="card-title"><?php echo($driver["name"]);?></h5>
            <p class="card-text">Driver Number: <?php safer_echo($driver["number"]); ?></p>
            <p class="card-text">Birthdate: <?php safer_echo($driver["birthdate"]);?></p>
            <p class="card-text">Country: <?php safer_echo($driver["country"]);?></p>
            <p class="card-text">GP's Entered: <?php safer_echo($driver["grands_prix_entered"]);?></p>
            <p class="card-text">World Championships: <?php safer_echo($driver["world_championships"]);?></p>
            <p class="card-text">Wins: <?php safer_echo($driver["highest_race_finish"]);?></p>
            <p class="card-text">Podiums: <?php safer_echo($driver["podiums"]);?></p>
            <p class="card-text">Career Points: <?php safer_echo($driver["career_points"]);?></p>
            <?php if(isset($driver["total_users"]) && $driver["total_users"] > 0) : ?>
                <p class="card-text"># of Associations: <?php safer_echo($driver["total_users"]);?></p>
            <?php endif; ?>
            <?php if(!isset($driver["user_id"])) : ?>
                <a class="btn btn-secondary" href="<?php echo get_url('api/detail_driver.php?driver_id=' .$driver["id"]); ?>" class="card-link">Add Driver</a>
            <?php endif; ?>
            <?php if(isset($driver["user_id"])) : ?>
                <?php if(has_role("Admin") && $remove="true") : ?>
                    <a class="btn btn-danger" href="<?php echo get_url('remove_driver.php?user_id=' .$driver["user_id"] . '&driver_id=' .$driver["id"]); ?>" class="card-link">Remove Driver</a>
                <?php endif; ?>
            <?php endif; ?>
            <a class="btn btn-secondary" href="<?php echo get_url('driver.php?id=' .$driver["id"]); ?>" class="card-link">View Driver</a>
        </div>
    </div>
<?php endif; ?>