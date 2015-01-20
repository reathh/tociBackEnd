<?php
require 'vendor/autoload.php';

require 'src/dbConnection.php';
require 'src/functions/userFunctions.php';
require 'src/functions/chatFunctions.php';

$app = new \Slim\Slim();

$app->post('/register', 'registerUser');
$app->post('/login', 'loginUser');
$app->post('/chat/addMessage', 'addMessage');

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

function addMessage() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $message = json_decode($request->getBody());

    try {
        $sessionKey = $request->headers->get('session_key');
        if ($sessionKey == null) {
            throw new Exception("Session key is empty");
        }
        $message->fromUserId = findUserBySessionToken($sessionKey);
    } catch (Exception $e) {
        $app->halt(400, "Not authorized");
    }

    if ($message->content === null || $message->content === "") {
        $app->halt(400, "Content cannot be empty");
    }

    if ($message->toUserId === null || $message->toUserId === "") {
        $app->halt(400, "Recipient's id cannot be empty");
    }
    $addedMessage = addMessageToDb($message);

    echo json_encode($addedMessage);
}