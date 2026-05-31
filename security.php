<?php
//require "db.php";
function Word_check($user_input){
    $normalize = [" ","　",".","．","。",",","，","、"];
    $target = $user_input;//$_POST["taxt"];
    for($i=0; $i<count($normalize); $i++){
        $target = str_replace($normalize[$i],"",$target);
    }
    $black_list = ["コーヒー","牛乳"];//get_forbidden_words();
    for($i=0; $i<count($black_list); $i++){
        if(mb_strpos($target,$black_list[$i]) !== False){
            return True;
        }
    }
    return False;//禁止文字なし
}
?>