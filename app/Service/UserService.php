<?php

namespace insectdie\PHP\MVC\Service;

use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Exception\ValidationException;
use insectdie\PHP\MVC\Model\UserRegisterRequest;
use insectdie\PHP\MVC\Model\UserRegisterResponse;
use insectdie\PHP\MVC\Repository\UserRepository;
use insectdie\PHP\MVC\Domain\User;
use insectdie\PHP\MVC\Model\UserLoginRequest;
use insectdie\PHP\MVC\Model\UserLoginResponse;

use function PHPUnit\Framework\throwException;

class UserService 
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse {
        $this->validateUserRegisterFunction($request);

        $user = $this->userRepository->findById($request->id);
        if($user != null) {
            throw new ValidationException("User is already exist!");
        }

        try {
            Database::beginTransaction();
            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
    
            $this->userRepository->save($user);
    
            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegisterFunction(UserRegisterRequest $request) 
    {
        if($request->id == null || $request->name == null || $request->password == null ||
        trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Name, Password can not blank");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or password is wrong");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("Id or password is wrong");
        }
    }

    public function validateUserLoginRequest(UserLoginRequest $request)
    {
        if($request->id == null || $request->password == null ||
        trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Password can not blank");
        }
    }
}