<?php
require_once 'controllers/ItemController.php';

$router = new Router();
$itemController = new ItemController();



$router->post('/items', function () use ($itemController) {
    $itemController->create();
});


$router->put('/items/{id}', function ($params) use ($itemController) {
    $itemController->update($params['id']);
});


$router->delete('/items/{id}', function ($params) use ($itemController) {
    $itemController->delete($params['id']);
});


$router->get('/items/{id}', function ($params) use ($itemController) {
    $itemController->get($params['id']);
});


$router->post('/login', function () use ($itemController) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $password = $data['password'];
    $result = $itemController->login($username, $password);
    if ($result) {
        $itemController->sendResponse(200, 'Login successful', $result);
    } else {
        $itemController->sendResponse(401, 'Login failed');
    }
});


$router->run();

