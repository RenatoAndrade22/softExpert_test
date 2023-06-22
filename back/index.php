<?php

include_once 'routers/api.php';
require_once 'helpers/ResponseHandler.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    header('HTTP/1.1 200 OK');
   
  }
  
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

$routes = api::routers();

// URL REQUEST
$requestUrl = $_SERVER['REQUEST_URI'];

// METHOD HTTP REQUEST
$requestMethod = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$requestMethod])) {
    foreach ($routes[$requestMethod] as $route => $controllerAction) {

        try{

            $pattern = '#^' . preg_replace('/{(\w+)}/', '(?<$1>[^/]+)', $route) . '$#';
            
            if (preg_match($pattern, $requestUrl, $matches)) {

                list($controllerName, $actionName) = explode('@', $controllerAction);
                
                loadController($controllerName);

                $controllerInstance = new $controllerName();
                $controllerInstance->$actionName($matches);
                
                exit();
            }

        }catch (Exception $e) {
            ResponseHandler::reponse('Error, please contact support.', 500, []);
        }
 
    }
}

function loadController($controllerName)
{
    include_once 'controllers/' . $controllerName . '.php';
}


