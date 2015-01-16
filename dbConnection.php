<?php
function getConnection() {
    $dbhost="localhost";
    $dbuser="reaths3_root";
    $dbpass="rootPassword";
    $dbname="reaths3_toci";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}