<?php

namespace insectdie\PHP\MVC\Controller;

use insectdie\PHP\MVC\App\View;
use insectdie\PHP\MVC\Config\Database;
use insectdie\PHP\MVC\Domain\Session;
use insectdie\PHP\MVC\Exception\ValidationException;
use insectdie\PHP\MVC\Model\UserRegisterRequest;
use insectdie\PHP\MVC\Model\UserLoginRequest;
use insectdie\PHP\MVC\Repository\SessionRepository;
use insectdie\PHP\MVC\Repository\UserRepository;
use insectdie\PHP\MVC\Service\SessionService;
use insectdie\PHP\MVC\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register(){
        View::render('User/register', [
            'title' => 'Register new User'
        ]);
    }

    public function postRegister() {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function login() {
        View::render('User/login', [
            "tittle" => "Login user"
        ]);
    }

    public function postLogin() {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            
            $this->sessionService->create($response->user->id);

            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                'title' => 'Login user',
                'error' => $exception->getMessage()
            ]);
        }
    }
}