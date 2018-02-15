<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @param User $user
     * @return array
     */
    public function findAllForUser($user) {

        $em = $this->getEntityManager();

        $sql = 'SELECT t1.amount, o.created_at, u.name, u.username, (SELECT SUM(amount) FROM transaction t_sum LEFT JOIN operation AS o_sum ON t_sum.operation_id=o_sum.id
                WHERE t_sum.account_id=t1.account_id AND o_sum.created_at<=o.created_at) AS cur_balance FROM transaction AS t1
            LEFT JOIN operation AS o ON t1.operation_id=o.id
            LEFT JOIN transaction AS t2 ON t1.operation_id=t2.operation_id AND t1.id<>t2.id
            LEFT JOIN user_account AS a ON t2.account_id=a.id
            LEFT JOIN user AS u ON a.user_id=u.id
            WHERE t1.account_id=?
            ORDER BY t1.id DESC'
        ;
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $user->getUserAccount()->getId());
        $stmt->execute();

        return $stmt->fetchAll();

    }
}
