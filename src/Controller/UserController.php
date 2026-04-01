<?php

namespace Controller;

use Model\User;
use Request\LoginRequest;
use Request\RegistrateRequest;
use Request\Request;
use Service\Auth\AuthServiceInterface;

class UserController
{
    public function __construct(private AuthServiceInterface $authService) {}

    public function getRegistrationForm(Request $request): void
    {
        require __DIR__ . '/../View/registrate.php';
    }

    public function handleRegistration(RegistrateRequest $request): void
    {
        $errors = $request->registrationValidate();

        if (User::getByEmail($request->getEmail())) {
            $errors['email'] = 'Email уже зарегистрирован';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../View/registrate.php';
            return;
        }

        User::create($request->getName(), $request->getEmail(), $request->getPassword());

        header('Location: /login');
        exit;
    }

    public function getLoginForm(Request $request): void
    {
        require __DIR__ . '/../View/login.php';
    }

    public function handleLogin(LoginRequest $request): void
    {
        $errors = $request->loginValidate();

        if (!empty($errors)) {
            require __DIR__ . '/../View/login.php';
            return;
        }

        if (!$this->authService->login($request->getLogin(), $request->getPassword())) {
            $errors['login'] = 'Неверный логин или пароль';
            require __DIR__ . '/../View/login.php';
            return;
        }

        header('Location: /catalog');
        exit;
    }
}