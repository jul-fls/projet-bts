<?php
    require_once "commons/global.php";
    session_start();
    if(isset($_COOKIE['rememberme'])){
        unset($_COOKIE['rememberme']);
        setcookie('rememberme',null,-1,'/');
    }
    session_destroy();
    session_regenerate_id(true);
    header('Location: '.$__WEB_ROOT__);
?>