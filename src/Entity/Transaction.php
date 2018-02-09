<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Operation
     * @ORM\ManyToOne(targetEntity="App\Entity\Operation", inversedBy="transactionsFrom")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id"))
     */
    private $operation;

    /**
     * @var UserAccount
     * @ORM\ManyToOne(targetEntity="App\Entity\UserAccount", inversedBy="transactionsFrom")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id",  nullable=false))
     */
    private $account;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2)
     */
    private $amount;

    /**
     * Transaction constructor.
     * @param UserAccount $account
     * @param float $amount
     */
    public function __construct($account, $amount)
    {
        $this->account = $account;
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserAccount
     */
    public function getAccount(): UserAccount
    {
        return $this->account;
    }

    /**
     * @param UserAccount $account
     */
    public function setAccount(UserAccount $account)
    {
        $this->account = $account;
    }

    /**
     * @return Operation
     */
    public function getOperation(): Operation
    {
        return $this->operation;
    }

    /**
     * @param Operation $operation
     */
    public function setOperation(Operation $operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}
