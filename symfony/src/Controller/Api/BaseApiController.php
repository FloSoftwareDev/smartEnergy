<?php

namespace App\Controller\Api;

use App\Service\ErrorHandlingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseApiController extends AbstractController
{
    protected ErrorHandlingService $errorHandler;

    public function __construct(ErrorHandlingService $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    protected function jsonSuccess($data = null, string $message = 'Success'): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function jsonError(string $message, int $code = 400): JsonResponse
    {
        return $this->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
} 