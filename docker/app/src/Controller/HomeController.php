<?php
declare(strict_types=1);
namespace App\Controller;

use App\Exception\Service\RequestStringNotFoundException;
use App\Service\StringService;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{

    public function index(): Response
    {
        return new Response("Hello from home controller", 200);
    }

    public function execute(StringService $stringService): Response
    {
        $request_string = $this->request->request->get('string');
        if (null === $request_string) {
            throw new RequestStringNotFoundException("Строка не найдена в теле запроса");
        }
        $response = $stringService->execute($request_string);
        return new Response($response, 200);
    }

}