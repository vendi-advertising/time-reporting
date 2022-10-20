<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserTimeEntry;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserTimeEntry>
 *
 * @method UserTimeEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTimeEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTimeEntry[]    findAll()
 * @method UserTimeEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTimeEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTimeEntry::class);
    }

    public function rollupReport(int $entryDateInt): array
    {
        $qb = $this->createQueryBuilder('ute');

        $query = $qb
            ->leftJoin('ute.project', 'p')
            ->leftJoin('ute.user', 'u')
            ->leftJoin('p.client', 'c')
            ->addSelect('p')
            ->addSelect('u')
            ->addSelect('c')
            ->andWhere('ute.entryDateInt = :entryDateInt')
            ->setParameter('entryDateInt', $entryDateInt)
            ->getQuery();

        return $query
            ->getResult();
    }

    /**
     * @param User $user
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @return UserTimeEntry[]
     */
    public function findAllByUserAndDateRange(User $user, DateTimeInterface $start, DateTimeInterface $end): array
    {
        $qb = $this->createQueryBuilder('e');

        return $qb
            ->andWhere('e.user = :user')
            ->andWhere($qb->expr()->between('e.entryDateInt', ':start', ':end'))
            ->setParameter('user', $user)
            ->setParameter('start', (int)$start->format('Ymd'))
            ->setParameter('end', (int)$end->format('Ymd'))
            ->getQuery()
            ->getResult();
    }

    public function save(UserTimeEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserTimeEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserTimeEntry[] Returns an array of UserTimeEntry objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserTimeEntry
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
