<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../models/PasswordGenerator.php';
require_once __DIR__ . '/../models/PasswordValidator.php';
require_once __DIR__ . '/../models/PasswordLog.php';
require_once __DIR__ . '/../resources/v1/PasswordResource.php';
require_once __DIR__ . '/../resources/v1/ValidatorResource.php';

$router = new Router('v1');

$router->addRoute('GET',  '/password',          function () {
    (new PasswordResource())->generate();
});
$router->addRoute('POST', '/passwords',         function () {
    (new PasswordResource())->generateMultiple();
});
$router->addRoute('POST', '/password/validate', function () {
    (new ValidatorResource())->validate();
});

$router->dispatch();