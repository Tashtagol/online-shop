<?php

namespace Service\Auth;

use Model\User;

class AuthSessionService implements AuthServiceInterface
{
    public function check (): bool
    {
        $this->sessionStart();
        return  isset($_SESSION['user_id']);
    }
    public function getCurrentUser(): ?User
    {
        if (!$this->check()) {
            return null;
        }
        $userId = (int) $_SESSION['user_id'];
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
            $_SESSION['user_id'] = $user->getId();
            return true;
    }
        return false;
    }
    public function logout(): void
    {
        $this->sessionStart();
        unset($_SESSION['user_id']);
    }

}