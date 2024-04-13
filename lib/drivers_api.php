<?php

function fetch_driver($search){
    $data = ["search" => $search];
    $endpoint = "https://api-formula-1.p.rapidapi.com/drivers";
    $isRapidAPI = true;
    $rapidAPIHost = "api-formula-1.p.rapidapi.com";
    $result = get($endpoint, "F1_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result=[]; 
    }
    
    if(isset($result["response"])){
        $result=$result["response"];

    }

    $query=[];

    foreach($result as $index => $row) {
        foreach ($row as $k => $v) {
            if($index === 0){
            array_push($result, "$k");
            }

            if($k==="country"){
                $v=$v["name"];

            }

            if($k==="highest_race_finish"){
                $v=$v["number"];
            }

            if($k==="teams"){
                continue;
            }
            $query["$k"] = $v;
            
        }
    }

    

    
    return $query;
}