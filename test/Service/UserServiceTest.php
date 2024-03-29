<?php

namespace insectdie\PHP\MVC\Service;

use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Exception\ValidationException;
use insectdie\PHP\MVC\Model\UserRegisterRequest;
use insectdie\PHP\MVC\Repository\UserRepository;
use insectdie\PHP\MVC\Domain\User;
use insectdie\PHP\MVC\Model\UserLoginRequest;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp():void {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess() {
        $request = new UserRegisterRequest();
        $request->id = "andry";
        $request->name = "Andry";
        $request->password = "rahasia";

        $response = $this->userService->register($request);

        self::assertEquals($response->user->id, $request->id);
        self::assertEquals($response->user->name, $request->name);
        self::assertNotEquals($response->user->password, $request->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed() {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate() {
        $user = new User();
        $user->id = "andry";
        $user->name = "Andry";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "andry";
        $request->name = "Andry";
        $request->password = "rahasia";

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
            
        $request = new UserLoginRequest();
        $request->id = "andry";
        $request->password = "rahasia";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "andry";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);
            
        $request = new UserLoginRequest();
        $request->id = "budi";
        $request->password = "salah";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "andry";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);
            
        $request = new UserLoginRequest();
        $request->id = "budi";
        $request->password = "rahasia";

        $response = $this->userService->login($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
}