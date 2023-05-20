<?php

require_once __DIR__ . '/../vendor/autoload.php';

use insectdie\PHP\MVC\App\Router;
use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Controller\HomeController;
use insectdie\PHP\MVC\Controller\UserController;

Database::getConnection('prod');

//Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

//User Controller
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);
Router::add('GET', '/users/login', UserController::class, 'login', []);
Router::add('POST', '/users/login', UserController::class, 'postLogin', []);

Router::run();