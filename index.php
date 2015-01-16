<?php
require 'vendor/autoload.php';

require 'dbConnection.php';
require 'userFunctions.php';

$app = new \Slim\Slim();

$app->post('/register', 'registerUser');

$app->run();

function registerUser() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $user = json_decode($request->getBody());

    try {
        $userWithId = addUserToTable($user);
        $userWithIdAndSessionKey = addSessionTokenToUser($userWithId);

        echo json_encode($userWithIdAndSessionKey);
    } catch(Exception $e) {
        $app->halt(500, $e->getMessage());
    }
}