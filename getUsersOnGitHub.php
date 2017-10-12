<?php

/* 
 * Trieu Nguyen wrote below code to get all user from Singapore on GitHub.
 * I used API search function located at: https://developer.github.com/v3/search/
 * I put parameter "location:singapore" when call the API.
 * Fully url to call API will be: https://api.github.com/search/users?q=location:singapore
 * This method only return 100 records per page. I loop to get all.
 * I put my client_id and client_secret to increase rate limit.
 */
set_time_limit(0);

function getResult($page, $item_per_page){
    $url = "https://api.github.com/search/users?q=location:singapore"."&page=".$page."&per_page=".$item_per_page;
    $cinit = curl_init();
    curl_setopt($cinit, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    curl_setopt($cinit, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cinit, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cinit, CURLOPT_URL, $url);
    $result = curl_exec($cinit);
    curl_close($cinit);
    
    $obj = json_decode($result);
    if($page == 1){
        echo "Total users: ". $obj->total_count. "<br/>";
        foreach ($obj->items as $idx=>$item){
            echo $idx."-". $item->login. "<br/>";
        }
        //option 1 loop and sleep after 60s.
//        return $obj->total_count;

        
        //option 2: paging
        
        paging($obj->total_count/$item_per_page);
        return;
    }
    foreach ($obj->items as $idx=>$item){
        echo $idx+(($page-1)*$item_per_page)."-". $item->login. "<br/>";
    }
    $totalpage = $obj->total_count/$item_per_page;
    //option 2: paging
    paging($totalpage);
}
/*
 * Option 1, loop and sleep every 60s
 */

/*
// Run first page with 100 result
$item_per_page = 100;
$rsl_obj = getResult(1, $item_per_page);
$totalpage = $rsl_obj/$item_per_page;
// Continue get more result from page 2nd.
for($int = 2; $int < $totalpage ; $int++ ){
    // sleep every 1 min after get 1000 result.
    if($int%10 == 0){
        sleep(60);
    }
    getResult($int, $item_per_page);
}
 */

/*
 *  Option 2, paging
 */
function paging($totalpage){
    for($int = 2; $int < $totalpage ; $int++ ){
        echo "<a href='?page=$int'>".$int."</a> | ";
    }
}

$item_per_page = 100;

if(isset($_GET) && !empty($_GET)){ 
    $page = $_GET['page'];
    getResult($page, $item_per_page);
}else{
    // First run
    $rsl_obj = getResult(1, $item_per_page);
}


