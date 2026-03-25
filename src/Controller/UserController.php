<?php
namespace Controller;

use Model\User;
use Model\OrderProduct;
use Request\RegistrateRequest;
use Request\Request;
use Request\LoginRequest;
use Service\Auth\AuthServiceInterface;
use Service\Auth\AuthSessionService;


class UserController
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this-> authService = $authService;
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
        if (User::getByEmail($request->getEmail())) {
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
        User::create($name, $email,$request->getPassword());

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
        $errors = $request->errors();

        if (!empty($errors)) {
            require_once './../View/login.php';
            return;
        }

        $user = User::getByEmail($request->getLogin());

        if (!$user || !password_verify($request->getPassword(), $user->getPassword())) {
            $errors['login'] = 'Неверный логин или пароль';
            require_once './../View/login.php';
            return;
        }

        $this->authService->login($user->getEmail(), $request->getPassword());

        // Получаем текущего пользователя через интерфейс
        $user= $this->authService->getCurrentUser();

        // Теперь можно безопасно перенаправлять на каталог
        header('Location: ./catalog');
        exit;
    }

}