<?php
namespace Controller;

use Model\User;
use Model\OrderProduct;
use Request\RegistrateRequest;
use Request\Request;
use Request\LoginRequest;

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

    public function handleRegistration(RegistrateRequest $request)
    {
        // валидация формата
        $errors = $request->registrationValidate();

        // проверка БД на уникальный email
        if ($this->userModel->getByEmail($request->getEmail())) {
            $errors['email'] = 'Email уже зарегистрирован';
        }

        // если есть ошибки — вернуть форму
        if (!empty($errors)) {
            require_once './../View/registrate.php';
            return;
        }

        // создание пользователя
        $name = $request->getName();
        $email = $request->getEmail();
        $password = password_hash($request->getPassword(), PASSWORD_DEFAULT);
        $this->userModel->create($name, $email, $password);

        header('Location: ./login');
        exit;
    }

    public function getLoginForm()
    {
        require_once './../View/login.php';
    }

    public function handleLogin(LoginRequest $request)
    {
        $request->loginValidate();
        $errors = $request->getErrors();

        if (!empty($errors)) {
            require_once './../View/login.php';
            return;
        }

        $user = $this->userModel->getByLogin($request->getLogin());

        if (!$user || !password_verify($request->getPassword(), $user->getPassword())) {
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

}