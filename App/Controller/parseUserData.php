<?php
// check laravel seeding info 
// setup relation database
// write API document
// build API

function getBookname($dissectUserStringData){
    $dissectObj = json_decode($dissectUserStringData); 
    $dissectArr = $dissectObj->purchaseHistory ?? null;
    return $dissectArr ? $dissectArr[0]->bookName : "no books here\n$dissectUserStringData";
}

$str = file_get_contents("./user_data.json");
$str = ltrim($str,"[");
$str = substr_replace($str,"",strrpos($str,"]"));

$pattern = "/cashBalance/";

// Process data into json formate
while(strlen($str) > 0){

    $endpos = strpos($str,"}",strpos($str,"}\n    ]\n")+1);
    $dissectUser = substr($str,0,$endpos+1);
    if(preg_match_all($pattern, $dissectUser)>1){
        $midpos = strrpos($dissectUser,"cashBalance")-7;
        $fisrtPart = rtrim(trim(substr($dissectUser,0,$midpos)),',');
        $secondPart = substr($dissectUser,$midpos);
        echo getBookname($fisrtPart);
        echo "\n---------------\n";
        echo getBookname($secondPart);
        echo "\n---------------\n";
    }
    else{
        echo getBookname($dissectUser);
        echo "\n---------------\n";
    }

    $str = substr($str,$endpos+3,strlen($str));
}    

?>