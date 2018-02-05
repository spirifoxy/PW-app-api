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
     * @var UserAccount
     * @ORM\ManyToOne(targetEntity="App\Entity\UserAccount", inversedBy="transactionsFrom")
     * @ORM\JoinColumn(name="account_from_id", referencedColumnName="id")
     */
    private $accountFrom;

    /**
     * @var UserAccount
     * @ORM\ManyToOne(targetEntity="App\Entity\UserAccount", inversedBy="transactionsTo")
     * @ORM\JoinColumn(name="account_to_id", referencedColumnName="id")
     */
    private $accountTo;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2)
     */
    private $amount;

    /**
     * \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

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
    public function getAccountFrom(): UserAccount
    {
        return $this->accountFrom;
    }

    /**
     * @param UserAccount $accountFrom
     */
    public function setAccountFrom(UserAccount $accountFrom)
    {
        $this->accountFrom = $accountFrom;
    }

    /**
     * @return UserAccount
     */
    public function getAccountTo(): UserAccount
    {
        return $this->accountTo;
    }

    /**
     * @param UserAccount $accountTo
     */
    public function setAccountTo(UserAccount $accountTo)
    {
        $this->accountTo = $accountTo;
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
