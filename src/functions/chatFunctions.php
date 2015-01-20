<?php
function addMessageToDb($message) {
    $sql = "INSERT INTO messages (fromUserId, toUserId, content)
    VALUES (:fromUserId, :toUserId, :content)";

    $db = getConnection();
    $query = $db->prepare($sql);
    $query->bindParam("fromUserId", $message->fromUserId);
    $query->bindParam("toUserId", $message->toUserId);
    $query->bindParam("content", $message->content);
    $succeeded = $query->execute();

    if (!$succeeded) {
        throw new Exception("There was a problem adding your message");
    }
    $message->id = $db->lastInsertId();
    return $message;
}