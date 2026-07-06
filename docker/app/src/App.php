<?php
declare(strict_types=1);
namespace App;

use App\Exception\Request\RequestBodyRequiredKeyNotSpecifiedException;
use App\Exception\Request\RequestValueHasIncorrectTypeException;
use App\Exception\Request\RequestValueIsEmptyException;
use App\Service\MailCheckerService;

class App
{
    public static function makeInstance(): App
    {
        return new App();
    }

    public function process()
    {
        try {
            $request_uri = $_SERVER['REQUEST_URI'];
            $get_pos = null;
            if (false !== $get_pos = strpos($request_uri, "?")) {
                $request_uri = substr($request_uri, 0, $get_pos);
            }

            if ("/" === $request_uri && 'POST' === $_SERVER['REQUEST_METHOD']) {
                $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
                $request_body = [];

                if ('application/json' === $content_type) {
                    $input = file_get_contents('php://input');
                    $request_body = ('' !== $input) ? json_decode($input, true) : [];
                }
                else {
                    $request_body = $_POST;
                }

                if (!array_key_exists('email_list', $request_body)) {
                    throw new RequestBodyRequiredKeyNotSpecifiedException("Ключ email_list не найден в теле запроса");
                }
                else if (array_key_exists('email_list', $request_body)) {
                    if (!is_array($request_body['email_list'])) {
                        throw new RequestValueHasIncorrectTypeException("Ключ email_list тела запроса должен быть массивом");
                    }
                    if (count($request_body['email_list']) === 0) {  
                        throw new RequestValueIsEmptyException("Массив по ключу email_list в теле запроса является пустым");
                    }
                }

                $mailChecker = MailCheckerService::makeInstance();
                $mailChecker->validateEmailList($request_body['email_list'], 'a', false);

                print_r($mailChecker->getCache());
               
            }
        }
        catch(\Exception $e) {

        }

    }

}