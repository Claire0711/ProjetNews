<?php
error_reporting(E_ALL & ~E_NOTICE);
try {
    $objPdo = new PDO('mysql:host=devbdd.iutmetz.univ-lorraine.fr;port=3306;dbname=thil32u_php', 'thil32u_appli', '07112001ClaireT', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (Exception $exception) {
    die($exception->getMessage());
}
