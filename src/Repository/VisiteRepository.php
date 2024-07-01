<?php
// src/Repository/VisiteRepository.php

namespace App\Repository;

use App\Entity\Visite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Visite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visite[]    findAll()
 * @method Visite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visite::class);
    }

    // Exemple de méthode personnalisée pour trouver des visites par type
    public function findByType($type)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.type = :type')
            ->setParameter('type', $type)
            ->orderBy('v.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // Exemple de méthode personnalisée pour trouver des visites dans un intervalle de dates
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('v.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    

}
