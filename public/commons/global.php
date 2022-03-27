<?php
    $__PATH__ = '/var/www/html/public/';
    require_once $__PATH__.'vendor/autoload.php';
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
    $dotenv->load();
    $__MAINTENANCE_MODE__ = intval(file_get_contents($__PATH__.'commons/maintenance.status'));
    $__MAIL_DOMAIN__ = $_SERVER['MAIL_DOMAIN'];
    $__DOMAIN__ = $_SERVER['DOMAIN'];
    $__WEB_ROOT__ = 'http://'.$__DOMAIN__.'/';
    
    function mb_ucfirst($string, $encoding){
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }
?>