<?php

namespace insectdie\PHP\MVC\Controller;

use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Domain\Session;
use insectdie\PHP\MVC\Domain\User;
use insectdie\PHP\MVC\Repository\SessionRepository;
use insectdie\PHP\MVC\Repository\UserRepository;
use insectdie\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase 
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp():void {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest() {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin() {
        $user = new User();
        $user->id = "andry";
        $user->name = "Andry";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();

        $this->expectOutputRegex("[Hello Andry]");
    }


}