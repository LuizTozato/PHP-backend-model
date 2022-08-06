<?php

    require_once(__DIR__."./utils.php");    

    //CORS config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header("Content-Type: application/json");

    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        resposta(200, true, '', '');
    };

?>