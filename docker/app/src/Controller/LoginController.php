<?php
declare(strict_types=1);
namespace App\Controller;

use App\Exception\Service\LoginNotSpecifiedException;
use App\Exception\Service\PasswordNotSpecifiedException;
use App\Service\LoginService;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login(LoginService $loginService): Response
    {
        $login = $this->request->request->get('login');
        $password = $this->request->request->get('password');

        if (!$login) {
            throw new LoginNotSpecifiedException("Не указан логин");
        }

        if (!$password) {
            throw new PasswordNotSpecifiedException("Не указан пароль");
        }
        $user_params =
        [
            'login' => $login,
            'password' => $password,
        ];

        $loginService->login($user_params);

        $this->request->getSession()->set('user', $user_params['login']);
        $this->request->getSession()->set('is_auth', true);

        return new Response("Авторизация прошла успешно", 201);

    }

    public function logout(): Response
    {
        $response_message = '';
        $status_code = 0;

        if ($this->request->getSession()->has('user') && $this->request->getSession()->has('is_auth') && $this->request->getSession()->get('is_auth') === true)  {
            $this->request->getSession()->remove('login');
            $this->request->getSession()->remove('is_auth');

            $response_message = "Вы успешно вышли из системы";
            $status_code = 201;

        }
        else {
            $response_message = "Вы не авторизованы";
            $status_code = 401;
        }

        return new Response($response_message, $status_code);
    }
}