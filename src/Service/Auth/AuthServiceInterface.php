<?php

namespace Service\Auth;
use Model\User;

interface AuthServiceInterface
{
    public function check():bool;
    public function getCurrentUser(): ?User;
    public function login(string $login, string $password):bool;
}