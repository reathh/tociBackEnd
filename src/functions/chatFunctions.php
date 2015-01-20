<?php
class Message {
    public $id;
    public $fromUserId;
    public $toUserId;
    public $content;
    public $createdAt;
    public $seenAt;
}
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

function getMessagesFromDb($from, $to, $pageSize, $pageNumber) {
    $limit = getLimitQueryForPaging($pageSize, $pageNumber);
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM messages WHERE fromUserId='$from' AND toUserId='$to' ORDER BY `createdAt` DESC" . $limit;

    $db = getConnection();
    $succeeded = $query = $db->query($sql);

    if (!$succeeded) {
        throw new Exception("There was a problem fetching messages");
    }

    $messages = $query->fetchAll(PDO::FETCH_OBJ);
    $howManyPagesQueryResult = $db->query('SELECT FOUND_ROWS();')->fetch(PDO::FETCH_COLUMN);
    $howManyPages = $pageSize === null ? 1 : ceil($howManyPagesQueryResult / $pageSize);

    $output = new stdClass();
    $output->pageCount = $howManyPages;
    $output->messages = $messages;
    return $output;
}

function getUserChronologyFromDb($userId, $pageSize, $pageNumber) {
    function chronologyOutput($toUserId) {
        return "toUserId: " . $toUserId;
    }

    $limit = getLimitQueryForPaging($pageSize, $pageNumber);
    $sql = "SELECT SQL_CALC_FOUND_ROWS toUserId, MAX(createdAt) FROM messages
     WHERE `fromUserId`='$userId'
     GROUP BY toUserId
     ORDER BY MAX(createdAt) DESC"
     . $limit;


    $db = getConnection();
    $succeeded = $query = $db->query($sql);

    if (!$succeeded) {
        throw new Exception("There was a problem fetching chronology");
    }
    $howManyPagesQueryResult = $db->query('SELECT FOUND_ROWS();')->fetch(PDO::FETCH_COLUMN);
    $howManyPages = $pageSize === null ? 1 : ceil($howManyPagesQueryResult / $pageSize);
    $chronologies = $query->fetchAll(PDO::FETCH_FUNC, "chronologyOutput");

    $output = new stdClass();
    $output->pageCount = $howManyPages;
    $output->chronologies = $chronologies;

    return $output;
}

function getLimitQueryForPaging($pageSize, $pageNumber) {
    $pageNumber = $pageNumber !== null ? $pageNumber : 1;
    $pageOffset = ($pageNumber * $pageSize)-$pageSize;
    $limit = $pageNumber !== null && $pageSize !== null ? " LIMIT $pageSize OFFSET $pageOffset" : "";
    return $limit;
}