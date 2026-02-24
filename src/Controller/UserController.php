<?php
namespace Controller;

use Model\User;
use Model\OrderProduct;

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function getRegistrationForm()
    {
        require_once './../View/registrate.php';
    }

    public function handleRegistration()
    {
        $errors = $this->registrationValidate($_POST);

        if (!empty($errors)) {
            require_once './../View/registrate.php';
            return;
        }

        $name = $_POST['Name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['psw'], PASSWORD_DEFAULT);

        $this->userModel->create($name, $email, $password);

        header('Location: ./login');
        exit;
    }

    public function getLoginForm()
    {
        require_once './../View/login.php';
    }

    public function handleLogin()
    {
        $errors = $this->loginValidate($_POST);

        if (!empty($errors)) {
            require_once './../View/login.php';
            return;
        }

        $user = $this->userModel->getByLogin($_POST['login']);

        if (!$user || !password_verify($_POST['password'], $user->getPassword())) {
            $errors['login'] = 'Неверный логин или пароль';
            require_once './../View/login.php';
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user->getId();

        header('Location: ./catalog');
        exit;
    }

    private function registrationValidate(array $data): array
    {
        $errors = [];

        if (empty($data['Name'])) $errors['Name'] = 'Имя обязательно';

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный email';
        } elseif ($this->userModel->getByEmail($data['email'])) {
            $errors['email'] = 'Email уже зарегистрирован';
        }

        if (empty($data['psw']) || strlen($data['psw']) < 5) {
            $errors['psw'] = 'Пароль должен быть минимум 5 символов';
        }

        if ($data['psw'] !== ($data['psw-repeat'] ?? null)) {
            $errors['psw-repeat'] = 'Пароли не совпадают';
        }

        return $errors;
    }

    private function loginValidate(array $data): array
    {
        $errors = [];

        if (empty($data['login'])) $errors['login'] = 'Введите email';
        if (empty($data['password'])) $errors['password'] = 'Введите пароль';

        return $errors;
    }
}