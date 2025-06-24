<?php

namespace App\Service;

use App\Entity\EnergyData;
use App\Repository\EnergyDataRepository;
use Doctrine\ORM\EntityManagerInterface;

class EnergyService
{
    private EntityManagerInterface $entityManager;
    private EnergyDataRepository $energyRepository;

    public function __construct(EntityManagerInterface $entityManager, EnergyDataRepository $energyRepository)
    {
        $this->entityManager = $entityManager;
        $this->energyRepository = $energyRepository;
    }

    public function getDashboardData(): array
    {
        $totalConsumption = $this->energyRepository->getTotalConsumption();
        $totalProduction = $this->energyRepository->getTotalProduction();
        $totalCost = $this->energyRepository->getTotalCost();
        $latestData = $this->energyRepository->getLatestData(5);

        return [
            'totalConsumption' => $totalConsumption,
            'totalProduction' => $totalProduction,
            'totalCost' => $totalCost,
            'netConsumption' => $totalConsumption - $totalProduction,
            'efficiency' => $totalProduction > 0 ? ($totalProduction / $totalConsumption) * 100 : 0,
            'latestData' => $latestData,
            'savings' => $this->calculateSavings($totalConsumption, $totalProduction)
        ];
    }

    public function addEnergyData(array $data): EnergyData
    {
        $energyData = new EnergyData();
        $energyData->setTimestamp(new \DateTime($data['timestamp'] ?? 'now'));
        $energyData->setConsumption($data['consumption'] ?? 0);
        $energyData->setProduction($data['production'] ?? 0);
        $energyData->setCost($data['cost'] ?? 0);
        $energyData->setSource($data['source'] ?? 'unknown');
        $energyData->setEfficiency($data['efficiency'] ?? null);

        $this->entityManager->persist($energyData);
        $this->entityManager->flush();

        return $energyData;
    }

    public function getDataByDateRange(\DateTime $start, \DateTime $end): array
    {
        return $this->energyRepository->getDataByDateRange($start, $end);
    }

    public function getDataBySource(string $source): array
    {
        return $this->energyRepository->getDataBySource($source);
    }

    private function calculateSavings(float $consumption, float $production): float
    {
        // Simple calculation: assume €0.25 per kWh
        $rate = 0.25;
        return $production * $rate;
    }

    public function generateSampleData(): void
    {
        $sources = ['Solar', 'Wind', 'Grid', 'Battery'];
        
        for ($i = 0; $i < 50; $i++) {
            $timestamp = new \DateTime("-{$i} hours");
            $consumption = rand(2, 8);
            $production = rand(0, 6);
            $cost = $consumption * 0.25;
            
            $data = [
                'timestamp' => $timestamp->format('Y-m-d H:i:s'),
                'consumption' => $consumption,
                'production' => $production,
                'cost' => $cost,
                'source' => $sources[array_rand($sources)],
                'efficiency' => $production > 0 ? ($production / $consumption) * 100 : null
            ];
            
            $this->addEnergyData($data);
        }
    }
} 