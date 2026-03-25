<?php

namespace Request;

class Request
{
    protected array $data = [];
    protected array $errors = []; // ← уже есть? ок
    protected string $method;

    public function __construct(string $method, array $data = [])
    {
        $this->method = $method;
        $this->data = $data;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }
    public function errors(): array
    {
        return $this->errors;
    }
}