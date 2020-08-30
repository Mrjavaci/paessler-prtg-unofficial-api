<?php
/**
 * Created by PhpStorm.
 * User: javaci
 * Date: 2020-08-30
 * Time: 12:22
 */

header('Content-Type: text/html; charset=utf-8');


$operation = $_POST["operation"];
include "class.Prtg.php";
switch ($operation) {
    case "anaInternet":
        $graphID = $_POST["id"];
        $interface = $_POST["interface"];
        $prtg = new Prtg();
        echo $prtg->getAnaInternet($interface, $graphID, $_POST["type"]);
        break;
    case "UserInternet":
        $graphID = $_POST["id"];
        $userName = $_POST["userName"];
        $prtg = new Prtg();
        echo $prtg->getUserInternet($graphID, $userName);
        break;
    default:
        echo "Operasyon Hatası.";
}