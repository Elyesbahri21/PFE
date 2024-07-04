<?php

namespace App\Repository;

use App\Entity\Contrat;
use App\Entity\Visite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use DateInterval;


/**
 * @extends ServiceEntityRepository<Contrat>
 *
 * @method Contrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrat[]    findAll()
 * @method Contrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }

    public function add(Contrat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $userId
     * @return Contrat[]
     */
    public function findByUserId(int $userId): array
    {

        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function remove(Contrat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findExpiringSoon(): array
    {
        $now = new \DateTime();
        $oneMonthLater = (clone $now)->modify('+1 month');
    
        return $this->createQueryBuilder('c')
            ->where('c.date_fin BETWEEN :now AND :oneMonthLater')
            ->setParameter('now', $now)
            ->setParameter('oneMonthLater', $oneMonthLater)
            ->getQuery()
            ->getResult();
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



    

//    /**
//     * @return Contrat[] Returns an array of Contrat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Contrat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

