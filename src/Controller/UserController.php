<?php
namespace Controller;
use Model\User;

class UserController
{
    private User $userModel;

    public function __construct()
    {
        // Теперь мы используем этот объект везде в классе
        $this->userModel = new User();
    }

    public function getRegistrationForm()
    {
        require_once './../View/registrate.php';
    }

    public function handleRegistration()
    {
        $errors = $this->registrationValidate($_POST);

        if (empty($errors)) {
            $name = $_POST['Name'];
            $email = $_POST['email'];
            $password = $_POST['psw'];
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Используем модель из конструктора
            $this->userModel->create($name, $email, $hash);

            header('Location: ./login');
            exit(); // Остановка выполнения после редиректа
        }

        require_once './../View/registrate.php';
    }

    private function registrationValidate($methodPost)
    {
        $errors = [];

        // Валидация имени
        if (isset($methodPost['Name'])) {
            $name = $methodPost['Name'];
            if (empty($name)) {
                $errors['Name'] = 'Имя не должно быть пустым';
            } elseif (strlen($name) < 3) {
                $errors['Name'] = 'Имя должно быть больше 3-х символов';
            }
        } else {
            $errors['Name'] = 'input name required';
        }

        // Валидация email
        if (isset($methodPost['email'])) {
            $email = $methodPost['email'];
            if (empty($email)) {
                $errors['email'] = 'Email не должен быть пустым';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Введите корректный Email';
            } else {
                // Используем модель из конструктора
                $userData = $this->userModel->getEmail($email);

                if ($userData !== false && $userData['email'] === $email) {
                    $errors['email'] = "Email уже зарегистрирован";
                }
            }
        }

        // Валидация пароля
        $password = $methodPost['psw'] ?? null;
        if (isset($methodPost['psw'])) {
            if (empty($password)) {
                $errors['psw'] = 'Пароль не должен быть пустым';
            } elseif (strlen($password) < 5) {
                $errors['psw'] = 'Пароль должен быть более 5-ти символов';
            } elseif (is_numeric($password)) {
                $errors['psw'] = 'Пароль не должен состоять только из цифр';
            } elseif ($password === strtolower($password) || $password === strtoupper($password)) {
                $errors['psw'] = 'Пароль должен содержать заглавные и строчные буквы';
            }
        } else {
            $errors['psw'] = 'input password required';
        }

        // Повтор пароля
        if (isset($methodPost['psw-repeat'])) {
            $passwordRepeat = $methodPost['psw-repeat'];
            if (empty($passwordRepeat)) {
                $errors['psw-repeat'] = 'Повторный пароль не должен быть пустым';
            } elseif ($password !== $passwordRepeat) {
                $errors['psw-repeat'] = 'Повторный пароль не совпадает с паролем';
            }
        }

        return $errors;
    }

    public function getLoginForm()
    {
        require_once './../View/login.php';
    }

    public function handleLogin()
    {
        $errors = $this->loginValidate($_POST);

        if (empty($errors)) {
            $login = $_POST['login'];
            $password = $_POST['password'];

            // Используем модель из конструктора
            $data = $this->userModel->getLogin($login);

            if ($data === false) {
                $errors['login'] = 'Неверный логин или пароль';
            } else {
                $passwordFromDB = $data['password'];
                if (password_verify($password, $passwordFromDB)) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['user_id'] = $data['id'];
                    header('Location: ./catalog');
                    exit(); // Остановка выполнения после редиректа
                } else {
                    $errors['password'] = 'Неверный логин или пароль';
                }
            }
        }

        // Рендерим вью в конце, если были ошибки
        require_once './../View/login.php';
    }

    private function loginValidate($methodPost)
    {
        $errors = [];
        if (empty($methodPost['login'])) {
            $errors['login'] = 'login is required';
        }
        if (empty($methodPost['password'])) {
            $errors['password'] = 'password is required';
        }
        return $errors;
    }
}