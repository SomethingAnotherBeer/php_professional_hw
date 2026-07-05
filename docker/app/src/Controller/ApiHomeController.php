<?php
declare(strict_types=1);
namespace App\Controller;

use App\Exception\Service\RequestStringNotFoundException;
use App\Service\StringService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiHomeController extends Controller
{
    public function index(): JsonResponse
    {
        return new JsonResponse(['response' => 'Hello from api home controller'], 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }


    public function execute(StringService $stringService): JsonResponse
    {
        $request_data = ("" !== $this->request->getContent()) ? json_decode($this->request->getContent(), true) : [];
        if (!array_key_exists('string', $request_data)) {
            throw new RequestStringNotFoundException("Строка не найдена в теле запроса");
        }

        $string = $request_data['string'];
        $response = $stringService->execute($string);
        return new JsonResponse(['response' => $response], 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

}