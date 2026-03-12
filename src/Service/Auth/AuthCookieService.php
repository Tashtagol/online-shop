<?php

namespace Service\Auth;

use Model\User;

class AuthCookieService implements AuthServiceInterface
{
    public function check (): bool
    {
        $this->sessionStart();
        return  isset($_COOKIE['user_id']);
    }
    public function getCurrentUser(): ?User
    {
        if (!$this->check()) {
            return null;
        }
        $userId = (int) $_COOKIE['user_id'];
        return User::getById($userId);


    }
    private function sessionStart():void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function login(string $login, string $password): bool
    {
        $user = User::getByLogin($login);
        if($user !== null && password_verify($password,$user->getPassword())) {
            $this->sessionStart();
            setcookie('user_id', (string)$user->getId(), time() + 3600 * 24 * 7, '/');;
            return true;
        }
        return false;
    }
    public function logout(): void
    {
        $this->sessionStart();
        setcookie('user_id', '', time() - 3600, '/');
    }
}