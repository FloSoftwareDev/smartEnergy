<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlingService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handleException(\Throwable $e, bool $isApiRequest = false): Response|JsonResponse
    {
        $this->logger->error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($isApiRequest) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // For regular web requests, return an error response
        return new Response(
            'Er is een fout opgetreden. Probeer het later opnieuw.',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
} 