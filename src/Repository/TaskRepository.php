<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getQbSearchByParams(
        ?User   $createdBy = null,
        ?string $sortBy = null,
        ?string $status = null,
        ?string $search = null,
        ?bool   $onlyNotExpired = false
    ): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('t');

        if (!is_null($createdBy)) {
            $queryBuilder
                ->andWhere('t.createdBy = :user')
                ->setParameter('user', $createdBy);
        }

        if ($sortBy === 'date_due_asc') {
            $queryBuilder->orderBy('t.dueDate', 'ASC');
        } elseif ($sortBy === 'date_due_desc') {
            $queryBuilder->orderBy('t.dueDate', 'DESC');
        }

        if ($onlyNotExpired) {
            $queryBuilder
                ->andWhere('t.dueDate >= :dueDate')
                ->setParameter('dueDate', new DateTime('now'));
        }

        if (!is_null($status)) {
            $queryBuilder
                ->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }

        if ($search) {
            $queryBuilder
                ->andWhere("tsplainquery(t.description,:search)=true")
                ->setParameter('search', $search);
        }
        return $queryBuilder;
    }

}
