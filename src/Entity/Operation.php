<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 * @HasLifecycleCallbacks
 */
class Operation
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Transaction[]
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="operation", cascade={"persist"})
     */
    private $transactions;

    /**
     * \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * Operation constructor.
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $this->createdAt = new \DateTime();
    }

    public function __toString() {
        return 'ID: ' . $this->getId() . '; Date: ' . $this->getCreatedAt()->format('Y-m-d H:i:s');
    }
}
