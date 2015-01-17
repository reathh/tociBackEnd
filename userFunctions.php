<?php
function addUserToDb($user) {
    checkForExistingUsername($user->username);
    checkForExistingEmail($user->email);

    $insertUserSQL = 'INSERT INTO users (username,authKey,email, fullName)
    VALUES (:username, :authKey, :email, :fullName)';

    $userAndPassword = $user->username . $user->password;
    $authKey = sha1($userAndPassword);

    $db = getConnection();
    $query = $db->prepare($insertUserSQL);
    $query->bindParam("username", $user->username);
    $query->bindParam("authKey", $authKey);
    $query->bindParam("email", $user->email);
    $query->bindParam("fullName", $user->fullName);
    $query->execute();

    $user->id = $db->lastInsertId();

    $db = null;
    unset($user->password);
    unset($user->email);
    return $user;
}

function checkForExistingEmail($email) {
    $sql = "SELECT EXISTS(SELECT 1 FROM users WHERE email='$email')";

    $db = getConnection();
    $query = $db->query($sql);
    if ($query->fetch()[0] == 1) {
        throw new Exception('Email already used');
    }
}
function checkForExistingUsername($username) {
    $sql = "SELECT EXISTS(SELECT 1 FROM users WHERE username='$username')";

    $db = getConnection();
    $query = $db->query($sql);
    if ($query->fetch()[0] == 1) {
        throw new Exception('Username already used');
    }
}

function addSessionKeyToUser($userWithId) {
    $interval = $userWithId->rememberUser == true ? '1 MONTH' : '1 DAY';
    $addNewSessionKeySQL = "INSERT INTO active_sessions (user_id, sessionKey, userAgent, ip, endAt)
    VALUES (:userId, :sessionKey, :userAgent, :ip, DATE_ADD(NOW(), INTERVAL $interval))";

    $request = \Slim\Slim::getInstance()->request();

    $userWithId->sessionKey = generateSessionKey($userWithId->id);

    $db = getConnection();
    $query = $db->prepare($addNewSessionKeySQL);
    $query->bindParam("userId", $userWithId->id);
    $query->bindParam("sessionKey", $userWithId->sessionKey);
    $query->bindParam("userAgent", $request->getUserAgent());
    $query->bindParam("ip", $request->getIp());
    $query->execute();

    $db = null;
    unset($userWithId->id);
    return $userWithId;
}
function generateSessionKey($userId) {
    $SessionKeyChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $sessionKeyLength = 100;
    $sessionKey = $userId;
    while (strlen($sessionKey) < $sessionKeyLength) {
        $sessionKey .= $SessionKeyChars[mt_rand(0, (strlen($SessionKeyChars)-1))];
    }
    return $sessionKey;
}

function findUserInDB($user) {
    $userAndPassword = $user->username . $user->password;
    $authKey = sha1($userAndPassword);

    $findUserSQL = "SELECT id, fullName FROM users WHERE authKey='$authKey'";

    $db = getConnection();
    $query = $db->query($findUserSQL);
    if (($result = $query->fetch()) == false) {
       throw new Exception('Invalid login credentials');
    }
    $user->id = $result[0];
    $user->fullName = $result[1];
    unset($user->password);
    return $user;
}