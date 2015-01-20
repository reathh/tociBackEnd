<?php
//$url = 'http://ivelinkirilov.com/Projects/TociChat/API/login';
//$data = array('username' => 'test', 'password' => 'test');
//
//// use key 'http' even if you send the request to https://...
//$options = array(
//    'http' => array(
//        'header'  => "Content-type: application/json\r\n",
//        'method'  => 'POST',
//        'content' => http_build_query($data),
//    ),
//);
//$context  = stream_context_create($options);
//$result = file_get_contents($url, false, $context);
//
//var_dump($result);
$url = 'http://ivelinkirilov.com/Projects/TociChat/API/login';
$data = array('key1' => 'value1', 'key2' => 'value2');

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

var_dump($result);