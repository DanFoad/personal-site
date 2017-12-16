<?php

$request = $_SERVER['REQUEST_URI'];
$request = explode('/', $request);

$method; $id;
if (count($request > 2)) {
    if (strcasecmp($request[2], 'get')) {
        $method = 'GET';
    }
}
if (count($request > 3)) {
    $id = $request[3];
}

header('Content-Type: application/json');

if (isset($id)) echo json_encode($id);