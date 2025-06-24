<?php

namespace App\Controller;

use App\Service\EnergyService;
use App\Service\ErrorHandlingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    private EnergyService $energyService;
    private ErrorHandlingService $errorHandler;

    public function __construct(EnergyService $energyService, ErrorHandlingService $errorHandler)
    {
        $this->energyService = $energyService;
        $this->errorHandler = $errorHandler;
    }

    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {
        try {
            $dashboardData = $this->energyService->getDashboardData();
            
            return $this->render('dashboard/index.html.twig', [
                'dashboardData' => $dashboardData
            ]);
        } catch (\Throwable $e) {
            return $this->errorHandler->handleException($e);
        }
    }

    #[Route('/api/dashboard-data', name: 'api_dashboard_data', methods: ['GET'])]
    public function getDashboardData(): Response
    {
        try {
            $data = $this->energyService->getDashboardData();
            return $this->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            return $this->errorHandler->handleException($e, true);
        }
    }

    #[Route('/api/energy-data', name: 'api_energy_data', methods: ['POST'])]
    public function addEnergyData(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ongeldige data ontvangen'
                ], 400);
            }

            $energyData = $this->energyService->addEnergyData($data);
            
            return $this->json([
                'success' => true,
                'message' => 'Energiedata succesvol toegevoegd',
                'data' => [
                    'id' => $energyData->getId(),
                    'timestamp' => $energyData->getTimestamp()->format('Y-m-d H:i:s'),
                    'consumption' => $energyData->getConsumption(),
                    'production' => $energyData->getProduction(),
                    'source' => $energyData->getSource()
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->errorHandler->handleException($e, true);
        }
    }
} 