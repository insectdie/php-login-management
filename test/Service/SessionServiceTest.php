<?php

namespace insectdie\PHP\MVC\Service;

function setcookie(string $name, string $value) {
    echo "$name: $value";
}

use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Exception\ValidationException;
use insectdie\PHP\MVC\Model\UserRegisterRequest;
use insectdie\PHP\MVC\Repository\SessionRepository;
use insectdie\PHP\MVC\Repository\UserRepository;
use insectdie\PHP\MVC\Domain\Session;
use insectdie\PHP\MVC\Domain\User;
use insectdie\PHP\MVC\Model\UserLoginRequest;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "andry";
        $user->name = "Andry";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testCreate() {

        $session = $this->sessionService->create("andry");

        $this->expectOutputRegex("[X-INSECTDIE-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals("andry", $result->userId);
    }

    public function testDestroy() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andry";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-INSECTDIE-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andry";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }

    
}