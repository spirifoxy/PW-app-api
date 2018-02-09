<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAccountRepository")
 * @HasLifecycleCallbacks
 */
class UserAccount
{
    const STATUS_ACTIVE = 0;
    const STATUS_BANNED = 1;

    const INITIAL_BALANCE_VALUE = 500;

    private static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BANNED => 'Banned',
    ];

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="userAccount")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Transaction[]
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="account", cascade={"persist"})
     */
    private $transactions;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, options={"default": 0})
     */
    private $balance = 0;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $status = 0;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * UserAccount constructor.
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->initializeBalance();
    }


    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return self::$statuses;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $date = new \DateTime();
        $this->createdAt = $date;
        $this->updatedAt = $date;
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->updatedAt = new \DateTime();
    }

    private function initializeBalance() {
        $this->addTransaction(self::INITIAL_BALANCE_VALUE);
    }

    public function addTransaction($amount, $operation = null)
    {
        $this->assertTransactionAllowed($amount);

        $transaction = new Transaction($this, $amount);
        if ($operation) {
            $transaction->setOperation($operation);
        }

        $this->transactions[] = $transaction;
        $this->balance += $amount;
        return $transaction;
    }

    private function assertTransactionAllowed($amount)
    {
        if ($this->getStatus() == self::STATUS_BANNED) {
            throw new Exception("You are not allowed to perform any operations");
        }
        if ($this->getBalance() + $amount < 0) {
            throw new Exception("You don't have enough PW to do that");
        }
    }

}
