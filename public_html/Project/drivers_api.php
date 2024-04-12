<?php

function fetch_drivers($search){
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

    return $result;
}
