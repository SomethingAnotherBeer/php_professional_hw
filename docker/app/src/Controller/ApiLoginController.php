<?php
declare(strict_types=1);
namespace App\Controller;

use App\Exception\Service\LoginNotSpecifiedException;
use App\Exception\Service\PasswordNotSpecifiedException;
use App\Service\LoginService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiLoginController extends Controller
{
    public function login(LoginService $loginService): JsonResponse
    {
        $request_data = ('' !== $this->request->getContent()) ? json_decode($this->request->getContent(), true) : [];
        if (!array_key_exists('login', $request_data)) {
            throw new LoginNotSpecifiedException("Не указан логин");
        }
        if (!array_key_exists('password', $request_data)) {
            throw new PasswordNotSpecifiedException("Не указан пароль");
        }

        $user_params =
        [
            'login' => $request_data['login'],
            'password' => $request_data['password'],
        ];

        $loginService->login($user_params);

        $this->request->getSession()->set('user', $user_params['login']);
        $this->request->getSession()->set('is_auth', true);

        return new JsonResponse(['response' => 'Авторизация прошла успешно'], 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE);

    }

    public function logout(): JsonResponse
    {
        $status_code = 0;
        $response_params = [];

        if ($this->request->getSession()->has('user') && $this->request->getSession()->has('is_auth') && $this->request->getSession()->get('is_auth') === true) {
            $this->request->getSession()->remove('user');
            $this->request->getSession()->remove('is_auth');

            $response_params['response'] = "Вы успешно вышли из системы";
            $status_code = 201;
        }
        else {
            $response_params['error'] = "Вы не авторизованы";
            $status_code = 401;
        }

        return new JsonResponse($response_params, $status_code)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}