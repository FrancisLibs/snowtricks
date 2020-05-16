<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function countAll()
	{
		return intval($this->createQueryBuilder('t')
			->select('COUNT(t)')
			->getQuery()->getSingleScalarResult());
    }
   
    /*
    * @return Trick[] Returns an array of Trick objects
    
    public function findPaginateComments($trickId, $nbComments)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :trickId')
            ->setParameter('trickId', $trickId )
            ->leftJoin('t.comments' , 'c')
            ->addSelect('c')
            ->leftJoin('t.pictures', 'p')
            ->addSelect('p')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    } */

    /*
    public function findOneBySomeField($value): ?Trick
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
