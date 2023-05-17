<?php


require_once 'config.php';
require_once 'database.php';
require_once 'utils/validation.php';
require_once 'models/Item.php';
require_once 'controllers/ItemController.php';
require_once 'routes.php';


$itemController = new ItemController();


$router = new Router();


$router->get('/items', function () use ($itemController) {
    return $itemController->index();
});

$router->post('/items', function () use ($itemController) {
    return $itemController->create();
});

$router->get('/items/:id', function ($id) use ($itemController) {
    return $itemController->get($id);
});

$router->put('/items/:id', function ($id) use ($itemController) {
    return $itemController->update($id);
});

$router->delete('/items/:id', function ($id) use ($itemController) {
    return $itemController->delete($id);
});


$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


$uri = parse_url($uri, PHP_URL_PATH);


switch ($method) {
    case 'GET':
        $router->dispatch('GET', $uri, $itemController);
        break;
    case 'POST':
        $router->dispatch('POST', $uri, $itemController);
        break;
    case 'PUT':
        $router->dispatch('PUT', $uri, $itemController);
        break;
    case 'DELETE':
        $router->dispatch('DELETE', $uri, $itemController);
        break;
    default:
        http_response_code(405);
        echo json_encode(array('error' => 'Method not allowed'));
        break;
}

