<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

if (!isSecure()) {
    header("HTTP/1.1 426 Upgrade Required");
    header("Upgrade: TLS/1.0, HTTP/1.1");
    header("Connection: Upgrade");
    die();
}

require_once 'config.php';
require_once 'jwt_helper.php';

$headers = getallheaders();
$method = $headers['Method'];

switch($method) {
    case 'start_session': startSession($db); break;
    case 'get_notes': getNotes($db); break;
    default: 
        header("HTTP/1.1 400 Bad Request");
        echo "<h1>HTTP 400 Bad Request</h1><br>Invalid request, try again with different parameters";
        die();
        break;
}

function isSecure() {
    return
      (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
      || $_SERVER['SERVER_PORT'] == 443;
  }

function startSession($db) {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        header("HTTP/1.1 401 Authorisation Required");
        die();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = 'SELECT id, username, password
            FROM users
            WHERE username = :username';
    $sth = $db->prepare($sql);
    $sth->execute(array(':username' => $username));
    $results = $sth->fetchAll();
    if (count($results) == 0) {
        header("HTTP/1.1 401 Authorisation Required");
        die();
    } 
    
    $db_pass = $results[0]['password'];
    
    if (!password_verify($password, $db_pass)) {
        header("HTTP/1.1 401 Authorisation Required");
        die();
    }

    $id = $results[0]['id'];
    $token = array();
    $token['id'] = $id;

    echo JWT::encode($token, SERVER_KEY);
}

function getNotes($db) {
    if (!isset($_POST['token'])) {
        header("HTTP/1.1 401 Authorisation Required");
        die();
    }

    $token = JWT::decode($_POST['token'], SERVER_KEY);
    $id = $token->id;

    $sql = 'SELECT id, name, date, text
            FROM notes
            WHERE user_id = :user_id';
    $sth = $db->prepare($sql);
    $sth->execute(array(':user_id' => $id));
    $results = $sth->fetchAll();

    echo json_encode($results);
}