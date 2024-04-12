<?php
require(__DIR__ . "/../../partials/nav.php");

if(isset($_GET["search"])){
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["search" => $_GET["search"]];
    $endpoint = "https://api-formula-1.p.rapidapi.com/drivers";
    $isRapidAPI = true;
    $rapidAPIHost = "api-formula-1.p.rapidapi.com";
    $result = get($endpoint, "F1_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }

    if(isset($result["response"])){
        $result=$result["response"];

    }





    $db=getDB();
    $query="INSERT INTO `Drivers` ";
    $columns=[];
    $params=[];
    foreach($result as $index => $row) {
        foreach ($row as $k => $v) {
            if($index === 0){
            array_push($columns, "$k");
            }

            if($k==="country"){
                $v=$v["name"];

            }

            if($k==="highest_race_finish"){
                $v=$v["position"];
            }

            if($k==="teams"){
                continue;
            }
            $params[":$k$index"] = $v;
            
        }
    }
    unset($columns[15]);


    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",",array_keys($params)) . ")";
    var_export($query);
    error_log(var_export($params, true));



    try{
        $stmt=$db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record", "success");
    }
    catch(PDOException $e){
        error_log("Something broke with the query" . var_export($e, true));
    }
}






?>
<div class="container-fluid">
    <h1>F1 stats</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Driver</label>
            <input name="search" />
            <input type="submit" value="Get driver" />
        </div>
    </form>
    <div class="row ">
        <?php if(isset($result)): ?>
                <pre>
                
                </pre>
            <?php foreach ($result as $name) : ?>
                <pre>
                <?php var_export($name); ?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");
