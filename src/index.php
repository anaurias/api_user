<?php

require './Controller/Usuario.php';

$method_name = $_SERVER['REQUEST_METHOD'];

header('Content-type: application/json');

$body = file_get_contents('php://input');

if ($method_name) {
    $rota = new Usuario();
    switch ($method_name) {
        case 'GET':
            $rota->buscaUsuario();
        case 'POST':
            $jsonBody = json_decode($body, true);
            $result = $rota->incluirUsuario($jsonBody);
            if ($result) {
                echo $result;
                exit();
            }
        case 'PUT':
            $jsonBody = json_decode($body, true);
            $result = $rota->updateUsuario($jsonBody);
            if ($result) {
                echo $result;
                exit();
            }
        case 'DELETE':
            $result = $rota->validaEmailPreenchido();
            if ($result) {
                echo $result;
                exit();
            }
            $rota->deleteUsuario($_REQUEST['email']);
    }
}
