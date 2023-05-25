<?php

namespace insectdie\PHP\MVC\Controller;

use insectdie\PHP\MVC\App\View;
use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Repository\SessionRepository;
use insectdie\PHP\MVC\Repository\UserRepository;
use insectdie\PHP\MVC\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index() : void 
    {
        $user = $this->sessionService->current();
        if($user == null) {
            View::render('Home/index',[
                "title" => "PHP Login Management"
            ]);
        } else {
            View::render('Home/dashboard',[
                "title" => "Dashboard",
                "user" => [
                    "name" => $user->name
                ]
            ]);
        }
    }
}