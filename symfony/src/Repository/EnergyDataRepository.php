<?php

namespace App\Repository;

use App\Entity\EnergyData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnergyData>
 *
 * @method EnergyData|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnergyData|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnergyData[]    findAll()
 * @method EnergyData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnergyDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnergyData::class);
    }

    public function getLatestData(int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.timestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getDataByDateRange(\DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.timestamp BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('e.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalConsumption(): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.consumption) as total')
            ->getQuery()
            ->getSingleScalarResult();
        
        return $result ?? 0.0;
    }

    public function getTotalProduction(): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.production) as total')
            ->getQuery()
            ->getSingleScalarResult();
        
        return $result ?? 0.0;
    }

    public function getTotalCost(): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.cost) as total')
            ->getQuery()
            ->getSingleScalarResult();
        
        return $result ?? 0.0;
    }

    public function getDataBySource(string $source): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.source = :source')
            ->setParameter('source', $source)
            ->orderBy('e.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 