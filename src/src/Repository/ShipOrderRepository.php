<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ShipOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShipOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipOrder[]    findAll()
 * @method ShipOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipOrder::class);
    }
}
