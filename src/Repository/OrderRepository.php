<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findTotalReceiptsByMonth($from = null, $to = null) {
        if ($from == null) {
            $from = new \DateTime('now');
            $from->modify('-1 year');
        }
        if ($to == null) {
            $to = new \DateTime('now');
        }

        $qb = $this
            ->createQueryBuilder('o')
            ->innerJoin('o.products', 'p')
            ->select('SUM(p.price) as sum, MONTH(o.date) as month, YEAR(o.date) as year')
            ->groupBy('year, month')
            ->andWhere('o.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function findBestSalesProducts($limit = null, $from = null, $to = null) {
        if ($limit == null) {
            $limit = 10;
        }
        if ($from == null) {
            $from = new \DateTime('now');
            $from->modify('-1 year');
        }
        if ($to == null) {
            $to = new \DateTime('now');
        }

        $qb = $this
            ->createQueryBuilder('o')
            ->select('p.name as product, COUNT(p.name) as amount')
            ->innerJoin('o.products', 'p')
            ->andWhere('o.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->groupBy('p.name')
            ->orderBy('amount', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    public function findMostProfitableProducts($limit = null, $from = null, $to = null) {
        if ($limit == null) {
            $limit = 10;
        }
        if ($from == null) {
            $from = new \DateTime('now');
            $from->modify('-1 year');
        }
        if ($to == null) {
            $to = new \DateTime('now');
        }

        $qb = $this
            ->createQueryBuilder('o')
            ->select('p.name as product, SUM(p.price) as amount')
            ->innerJoin('o.products', 'p')
            ->andWhere('o.date BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->groupBy('p.name')
            ->orderBy('amount', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getArrayResult();
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
