<?php
function addUserToTable($user) {
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

function addSessionTokenToUser($userWithId) {
    $addNewSessionKeySQL = 'INSERT INTO active_sessions (user_id, sessionKey, userAgent, ip, endAt)
    VALUES (:userId, :sessionKey, :userAgent, :ip, DATE_ADD(NOW(), INTERVAL 1 DAY))';

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
    $sessionKeyLength = 50;
    $sessionKey = $userId;
    while (strlen($sessionKey) < $sessionKeyLength) {
        $sessionKey .= $SessionKeyChars[mt_rand(0, (strlen($SessionKeyChars)-1))];
    }
    return $sessionKey;
}