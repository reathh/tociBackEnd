<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->post('/register', 'registerUser');

$app->run();

function registerUser() {
    $request = \Slim\Slim::getInstance()->request();
    $user = json_decode($request->getBody());

    $userAndPassword = $user->username . $user->password;
    $authKey = sha1($userAndPassword);

    $sql = 'INSERT users (username,authKey,email, fullName)
VALUES (:username, :authKey, :email, :fullName)';

    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $user->username);
        $stmt->bindParam("authKey", $authKey);
        $stmt->bindParam("email", $user->email);
        $stmt->bindParam("fullName", $user->fullName);
        $stmt->execute();
        $db = null;
        echo json_encode($user);
    } catch(PDOException $e) {
        echo json_encode('{"error":{"text":'. $e->getMessage() .'}}');
    }
}

function getConnection() {
    $dbhost="localhost";
    $dbuser="reaths3_root";
    $dbpass="rootPassword";
    $dbname="reaths3_toci";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}