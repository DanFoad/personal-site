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

if (!isset($_POST['method'])) {
    header("HTTP/1.1 400 Bad Request");
    echo "<h1>HTTP 400 Bad Request</h1><br>Invalid request, try again with different parameters";
    die();
}

$method = $_POST['method'];

switch($method) {
    case 'start_session': startSession($db); break;
    case 'get_notes': getNotes($db); break;
    case 'get_tags': getTags($db); break;
    case 'update_note': updateNote($db); break;
    case 'create_note': createNote($db); break;
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

function getID() {
    if (!isset($_POST['token'])) {
        header("HTTP/1.1 401 Authorisation Required");
        die();
    }

    $token = JWT::decode($_POST['token'], SERVER_KEY);
    $id = $token->id;

    return $id;
}

function getNotes($db) {
    $id = getID();

    $sql = 'SELECT id, name, date, text
            FROM notes
            WHERE user_id = :user_id';
    $sth = $db->prepare($sql);
    $sth->execute(array(':user_id' => $id));
    $results = $sth->fetchAll();

    echo json_encode($results);
}

function getTags($db) {
    $id = getID();

    $sql = 'SELECT id, name
            FROM tag
            WHERE user_id = :user_id';
    $sth = $db->prepare($sql);
    $sth->execute(array(':user_id' => $id));
    $results = $sth->fetchAll();

    echo json_encode($results);
}

function updateNote($db) {
    $id = getID();

    if (!isset($_POST['note'])) {
        header("HTTP/1.1 400 Bad Request");
        echo "<h1>HTTP 400 Bad Request</h1><br>Invalid request, try again with different parameters";
        die();
    }

    $note = json_decode($_POST['note'], true);
    
    $sql = 'UPDATE notes
            SET name = :name, tag = :tag, date = :date, text = :text
            WHERE id = :id';
    $sth = $db->prepare($sql);
    $sth->execute(array(':name' => $note['name'], ':tag' => $note['tag'], ':date' => $note['date'], ':text' => $note['text'], ':id' => $note['id']));

    $response = array();
    if ($sth->rowCount() > 0) {
        $response['success'] = true;
        echo json_encode($response);
    } else {
        $response['success'] = false;
        echo json_encode($response);
    }
}

function createNote($db) {
    $id = getID();

    if (!isset($_POST['note'])) {
        header("HTTP/1.1 400 Bad Request");
        echo "<h1>HTTP 400 Bad Request</h1><br>Invalid request, try again with different parameters";
        die();
    }

    $note = json_decode($_POST['note'], true);
    
    $sql = 'INSERT INTO notes (name, user_id, tag, date, text)
            VALUES (:name, :user_id, :tag, :date, :text)';
    $sth = $db->prepare($sql);
    $sth->execute(array(':name' => $note['name'], ':user_id' => $id, ':tag' => $note['tag'], ':date' => $note['date'], ':text' => $note['text']));

    $response = array();
    if ($sth->rowCount() > 0) {
        $response['success'] = true;
        $response['id'] = $db->lastInsertId();
        echo json_encode($response);
    } else {
        $response['success'] = false;
        echo json_encode($response);
    }
}
