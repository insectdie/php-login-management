<?php

namespace insectdie\PHP\MVC\Repository;

use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Repository\SessionRepository;
use insectdie\PHP\MVC\Domain\Session;
use insectdie\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "andry";
        $user->name = "Andry";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testSaveSuccess() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andry";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);
    }

    public function testDeleteByIdSuccess() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andry";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);

        $result = $this->sessionRepository->deleteById($session->id);
        self::assertNull($result);
    }

    public function testFindByIdNotFound() {
        $session = $this->sessionRepository->findById("notfound");
        self::assertNull($session);
    }
}