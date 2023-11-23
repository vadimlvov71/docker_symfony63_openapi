<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }


    /**
     * @param string $title
     * @param int $user_id
     * 
     * @return array
     */
    public function findByTitle(string $title, int $user_id): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM App\Entity\Task t WHERE t.title LIKE :title
                AND t.user_id = :user_id'
            )
            ->setParameter('title', '%' . $title . '%')
            ->setParameter('user_id', $user_id)
            ->getResult();
    }
    /**
     * @param string $description
     * @param int $user_id
     * 
     * @return array
     */
    public function findByDescription(string $description, int $user_id): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM App\Entity\Task t WHERE t.description LIKE :description
                AND t.user_id = :user_id'
            )
            ->setParameter('description', '%' . $description . '%')
            ->setParameter('user_id', $user_id)
            ->getResult();
    }


    /**
     * @param int $user_id
     * @param string $priority_sort
     * @param string $created_sort
     * 
     * @return array
     */
    public function sortBy(int $user_id, string $priority_sort, string $created_sort): array
    {
        $query = $this->createQueryBuilder('t');
        
        $query->andWhere('t.user_id = :user_id')
            ->setParameter('user_id', $user_id);
        if ($created_sort == "asc") {
            $query->addOrderBy('t.createdAt', 'ASC');
        } else {
            $query->addOrderBy('t.createdAt', 'DESC');
        }
        if ($priority_sort == "asc") {
            $query->addOrderBy('t.priority', 'ASC');
        } else {
            $query->addOrderBy('t.priority', 'desc');
        }
        $query =  $query->getQuery();
        return $query->getResult();
    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
