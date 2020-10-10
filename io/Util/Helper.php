<?php

function pre($data,$stop = true){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if($stop) exit;
}

function view($data,$stop = false){
    echo '===>'.$data."\n";
    if($stop) exit;
}

function basePath(){
    return __DIR__ . '/../';
}