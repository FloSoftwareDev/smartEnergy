<?php

namespace App\Controller\Api;

use App\Service\DatabaseService;
use App\Service\ErrorHandlingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class DataController extends BaseApiController
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService, ErrorHandlingService $errorHandler)
    {
        parent::__construct($errorHandler);
        $this->databaseService = $databaseService;
    }

    #[Route('/data', name: 'get_data', methods: ['GET'])]
    public function getData(Request $request): JsonResponse
    {
        try {
            $data = $this->databaseService->fetchData();
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->errorHandler->handleException($e, true);
        }
    }

    #[Route('/data', name: 'save_data', methods: ['POST'])]
    public function saveData(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->jsonError('Ongeldige data ontvangen');
            }

            $result = $this->databaseService->saveData($data);
            return $this->jsonSuccess($result, 'Data succesvol opgeslagen');
        } catch (\Throwable $e) {
            return $this->errorHandler->handleException($e, true);
        }
    }
} 