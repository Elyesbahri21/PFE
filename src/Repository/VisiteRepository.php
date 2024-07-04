<?php
// src/Repository/VisiteRepository.php

namespace App\Repository;

use App\Entity\Visite;
use App\Entity\User;
use App\Entity\Contrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateInterval;

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

    // // Exemple de méthode personnalisée pour trouver des visites par type
    // public function findByType($type)
    // {
    //     return $this->createQueryBuilder('v')
    //         ->andWhere('v.type = :type')
    //         ->setParameter('type', $type)
    //         ->orderBy('v.date', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // // Exemple de méthode personnalisée pour trouver des visites dans un intervalle de dates
    // public function findByDateRange(\DateTime $startDate, \DateTime $endDate)
    // {
    //     return $this->createQueryBuilder('v')
    //         ->andWhere('v.date BETWEEN :startDate AND :endDate')
    //         ->setParameter('startDate', $startDate)
    //         ->setParameter('endDate', $endDate)
    //         ->orderBy('v.date', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    public function findByResponsable(User $responsable): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.responsable = :responsable')
            ->setParameter('responsable', $responsable)
            ->getQuery()
            ->getResult();
    }

    public function countTotalVisitesForUser(User $user): int
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.responsable = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCompletedVisites(User $user, \DateTime $currentDate): int
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->where('v.date < :currentDate')
            ->andWhere('v.responsable = :user')
            ->setParameter('currentDate', $currentDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUpcomingVisites(User $user, \DateTime $currentDate): int
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->where('v.date >= :currentDate')
            ->andWhere('v.responsable = :user')
            ->setParameter('currentDate', $currentDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countVisitesByType(User $user, string $type): int
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.type = :type')
            ->andWhere('v.responsable = :user')
            ->setParameter('type', $type)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function createThreeVisites(Contrat $contrat)
    {
        $now = new \DateTime();
        for ($i = 0; $i < 3; $i++) {
            $visite = new Visite();
            $visite->setDate((clone $now)->add(new DateInterval('P' . ($i * 4) . 'M')));
            $visite->setType('préventive'); // Set your type accordingly
            $visite->setDescription('Description'); // Set your description accordingly
            $visite->setPv('PV'); // Set your pv accordingly
            $visite->setContrat($contrat);
            $visite->setResponsable(null);

            $this->entityManager->persist($visite);
        }

        $this->entityManager->flush();
    }
}
