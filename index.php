<?php
require 'vendor/autoload.php';

require 'src/dbConnection.php';
require 'src/functions/userFunctions.php';
require 'src/functions/chatFunctions.php';

$app = new \Slim\Slim();

$app->post('/register', 'registerUser');
$app->post('/login', 'loginUser');

$app->post('/chat/message', 'addMessage');

$app->get('/chat/user/messages/:to', 'getMessages');
$app->get('/chat/user/chronology', 'getUserChronology');

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

    $message->fromUserId = tryFindingUserByHeaderSessionKey();

    if (isNullOrEmpty($message->content)) {
        $app->halt(400, "Content cannot be empty");
    }

    if (isNullOrEmpty($message->toUserId)) {
        $app->halt(400, "Recipient's id cannot be empty");
    }
    $addedMessage = addMessageToDb($message);

    echo json_encode($addedMessage);
}

function getMessages($to) {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();

    $pageNumber = $request->get('pageNumber');
    $pageSize = $request->get('pageSize');

    if (isNullOrEmpty($to)) {
        $app->halt(400, "to cannot be empty");
    }
    $from = tryFindingUserByHeaderSessionKey();

    $messages = getMessagesFromDb($from, $to, $pageSize, $pageNumber);

    echo json_encode($messages);
}

function getUserChronology() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $pageNumber = $request->get('pageNumber');
    $pageSize = $request->get('pageSize');

    $userId = tryFindingUserByHeaderSessionKey();

    $chronology = getUserChronologyFromDb($userId, $pageSize, $pageNumber);

    echo json_encode($chronology);
}

function isNullOrEmpty($value) {
    if ($value === null || $value === "") {
        return true;
    }
    return false;
}