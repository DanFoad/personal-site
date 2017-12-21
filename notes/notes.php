<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

header('Content-Type: application/json');

if (!isSecure()) {
    header("HTTP/1.1 426 Upgrade Required");
    header("Upgrade: TLS/1.0, HTTP/1.1");
    header("Connection: Upgrade");
    die();
}

require_once 'config.php';
require_once 'jwt_helper.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['method'])) {
    header("HTTP/1.1 400 Bad Request");
    $response = array();
    $response['success'] = false;
    $response['message'] = 'No action specified';
    echo json_encode($response);
    die();
}

$method = $data['method'];

switch($method) {
    case 'start_session': startSession($db, $data); break;
    case 'get_notes': getNotes($db, $data); break;
    case 'get_tags': getTags($db, $data); break;
    case 'update_note': updateNote($db, $data); break;
    case 'create_note': createNote($db, $data); break;
    default: 
        header("HTTP/1.1 400 Bad Request");
        $response = array();
        $response['success'] = false;
        $response['message'] = 'No action specified';
        echo json_encode($response);
        die();
        break;
}

function isSecure() {
    return
      (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
      || $_SERVER['SERVER_PORT'] == 443;
  }

function startSession($db, $data) {
    if (!isset($data['username']) || !isset($data['password'])) {
        header("HTTP/1.1 401 Authorisation Required");
        $response = array();
        $response['success'] = false;
        $response['message'] = 'No username/password specified';
        echo json_encode($response);
        die();
    }

    $username = $data['username'];
    $password = $data['password'];

    $sql = 'SELECT id, username, password
            FROM users
            WHERE username = :username';
    $sth = $db->prepare($sql);
    $sth->execute(array(':username' => $username));
    $results = $sth->fetchAll();
    if (count($results) == 0) {
        header("HTTP/1.1 401 Authorisation Required");
        $response = array();
        $response['success'] = false;
        $response['message'] = 'Invalid username and/or password';
        echo json_encode($response);
        die();
    } 
    
    $db_pass = $results[0]['password'];
    
    if (!password_verify($password, $db_pass)) {
        header("HTTP/1.1 401 Authorisation Required");
        $response = array();
        $response['success'] = false;
        $response['message'] = 'Invalid username and/or password';
        echo json_encode($response);
        die();
    }

    $id = $results[0]['id'];
    $token = array();
    $token['id'] = $id;

    echo JWT::encode($token, SERVER_KEY);
}

function getID() {
    if (!isset($data['token'])) {
        header("HTTP/1.1 401 Authorisation Required");
        die();
    }

    $token = JWT::decode($data['token'], SERVER_KEY);
    $id = $token->id;

    return $id;
}

function getNotes($db, $data) {
    $id = getID();

    $sql = 'SELECT id, name, date, text
            FROM notes
            WHERE user_id = :user_id';
    $sth = $db->prepare($sql);
    $sth->execute(array(':user_id' => $id));
    $results = $sth->fetchAll();

    echo json_encode($results);
}

function getTags($db, $data) {
    $id = getID();

    $sql = 'SELECT id, name
            FROM tag
            WHERE user_id = :user_id';
    $sth = $db->prepare($sql);
    $sth->execute(array(':user_id' => $id));
    $results = $sth->fetchAll();

    echo json_encode($results);
}

function updateNote($db, $data) {
    $id = getID();

    if (!isset($data['note'])) {
        header("HTTP/1.1 400 Bad Request");
        $response = array();
        $response['success'] = false;
        $response['message'] = 'No note specified';
        echo json_encode($response);
        die();
    }

    $note = json_decode($data['note'], true);
    
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

function createNote($db, $data) {
    $id = getID();

    if (!isset($data['note'])) {
        header("HTTP/1.1 400 Bad Request");
        $response = array();
        $response['success'] = false;
        $response['message'] = 'No note specified';
        echo json_encode($response);
        die();
    }

    $note = json_decode($data['note'], true);
    
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
