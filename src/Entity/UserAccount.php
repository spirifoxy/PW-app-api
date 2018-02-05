<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAccountRepository")
 * @HasLifecycleCallbacks
 */
class UserAccount
{
    const STATUS_ACTIVE = 0;
    const STATUS_BANNED = 1;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="accountFrom")
     */
    private $transactionsFrom;

    /**
     * @var Transaction[]
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="accountTo")
     */
    private $transactionsTo;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2)
     */
    private $balance;

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
    public function getTransactionsFrom()
    {
        return $this->transactionsFrom;
    }

    /**
     * @param Transaction[] $transactionsFrom
     */
    public function setTransactionsFrom($transactionsFrom)
    {
        $this->transactionsFrom = $transactionsFrom;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactionsTo()
    {
        return $this->transactionsTo;
    }

    /**
     * @param Transaction[] $transactionsTo
     */
    public function setTransactionsTo($transactionsTo)
    {
        $this->transactionsTo = $transactionsTo;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
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
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->updatedAt = new \DateTime();
    }
}
