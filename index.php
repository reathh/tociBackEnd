<?php
require 'vendor/autoload.php';

require 'dbConnection.php';
require 'userFunctions.php';

$app = new \Slim\Slim();

$app->post('/register', 'registerUser');
$app->post('/login', 'loginUser');

$app->run();

function registerUser() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $user = json_decode($request->getBody());

    try {
        $userWithId = addUserToDb($user);
        $userWithIdAndSessionKey = addSessionKeyToUser($userWithId);

        echo json_encode($userWithIdAndSessionKey);
    } catch(Exception $e) {
        $app->halt(500, $e->getMessage());
    }
}

function loginUser() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $user = json_decode($request->getBody());

    try {
        $foundUser = findUserInDB($user);
        $userWithSessionKey = addSessionKeyToUser($foundUser);
        echo json_encode($userWithSessionKey);
    } catch(Exception $e) {
        $app->halt(500, $e->getMessage());
    }
}