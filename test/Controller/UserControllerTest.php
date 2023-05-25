<?php

namespace insectdie\PHP\MVC\App {
    function header(string $value) {
        echo $value;
    }
}

namespace insectdie\PHP\MVC\Service {
    function setcookie(string $name, string $value) {
        echo "$name: $value";
    }
}

namespace insectdie\PHP\MVC\Controller {
    use insectdie\PHP\MVC\Config\Database;
    use insectdie\PHP\MVC\Domain\User;
    use insectdie\PHP\MVC\Repository\SessionRepository;
    use insectdie\PHP\MVC\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp():void {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister() {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }

        public function testPostRegisterSuccess() {
            $_POST['id'] = 'rani';
            $_POST['name'] = 'Rani';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();    

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testPostRegisterValidationError() {
            $_POST['id'] = '';
            $_POST['name'] = 'Eko';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Id, Name, Password can not blank]");
        }

        public function testPostRegisterDuplicate() {
            $user = new User();
            $user->id = "andry";
            $user->name = "Andry";
            $user->password = "rahasia";

            $this->userRepository->save($user);

            $_POST['id'] = 'andry';
            $_POST['name'] = 'andry';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[User is already exist!]");
        }

        public function testLogin() {
            $this->userController->login();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
        }

        public function testLoginSuccess() {
            $user = new User();
            $user->id = "andry";
            $user->name = "Andry";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'andry';
            $_POST['password'] = 'rahasia';

            $this->userController->postLogin();    

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-INSECTDIE-SESSION: ]");
        }

        public function testLoginValidationError() {
            $_POST['id'] = '';
            $_POST['password'] = '';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id, Password can not blank]");
        }

        public function testLoginUserNotFound() {
            $_POST['id'] = 'rani';
            $_POST['password'] = 'asd';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLoginWrongPassword() {
            $user = new User();
            $user->id = "andry";
            $user->name = "Andry";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'andry';
            $_POST['password'] = 'salah';

            $this->userController->postLogin(); 

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }
    }
}