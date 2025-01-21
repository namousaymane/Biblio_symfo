<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function findBySearch(string $query): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.titre LIKE :query OR l.auteur LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
