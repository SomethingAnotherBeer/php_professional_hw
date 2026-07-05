<?php
declare(strict_types=1);
namespace App\Service;

use App\Exception\Service\InvalidAuthDataException;
use App\Exception\Service\LoginNotSpecifiedException;
use App\Exception\Service\PasswordNotSpecifiedException;
use App\Exception\Validation\IncorrectTypeException;
use App\Infrastructure\Connection;

class LoginService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function login(array $user_params): array
    {
        $connection = $this->connection->getConnection();

        $login = $user_params['login'] ?? null;
        $password = $user_params['password'] ?? null;

        if (!$login) {
            throw new LoginNotSpecifiedException("Не указан логин");
        }
        if (!$password) {
            throw new PasswordNotSpecifiedException("Не указан пароль");
        }

        if (!is_string($login)) {
            throw new IncorrectTypeException("Логин должен быть строкой");
        }

        if (!is_string($password)) {
            throw new IncorrectTypeException("Пароль должен быть строкой");
        }

        $sql = "SELECT id, user_name, user_login, user_password FROM users WHERE user_login = :user_login";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['user_login' => $login]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || ($user && !password_verify($password, $user['user_password']))) {
            throw new InvalidAuthDataException("Неправильный логин или пароль");
        }

        return 
        [   
            'id' => $user['id'],
            'user_name' => $user['user_name'],
            'user_login' => $user['user_login'],

        ];
        
    }
}