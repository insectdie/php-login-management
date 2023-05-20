<?php

namespace insectdie\PHP\MVC\Repository;

use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Repository\UserRepository;
use insectdie\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess() {
        $user = new User();
        $user->id = "andry";
        $user->name = "Andry";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound() {
        $user = $this->userRepository->findById("notfound");
        self::assertNull($user);
    }
}