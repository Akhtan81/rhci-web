<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="credit_cards")
 * @ORM\Entity(repositoryClass="App\Repository\CreditCardRepository")
 */
class CreditCard
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Groups("api_v1")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     *
     * @JMS\Groups("api_v1")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $holder;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3, nullable=false)
     */
    private $cvc;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $month;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $confirmedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $isConfirmed;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $isExpired;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->isConfirmed = false;
        $this->isExpired = false;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getHolder(): ?string
    {
        return $this->holder;
    }

    /**
     * @param string $holder
     */
    public function setHolder(?string $holder): void
    {
        $this->holder = $holder;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCvc(): ?string
    {
        return $this->cvc;
    }

    /**
     * @param string $cvc
     */
    public function setCvc(?string $cvc): void
    {
        $this->cvc = $cvc;
    }

    /**
     * @return string
     */
    public function getMonth(): ?string
    {
        return $this->month;
    }

    /**
     * @param string $month
     */
    public function setMonth(?string $month): void
    {
        $this->month = $month;
    }

    /**
     * @return string
     */
    public function getYear(): ?string
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear(?string $year): void
    {
        $this->year = $year;
    }

    /**
     * @return \DateTime
     */
    public function getConfirmedAt(): ?\DateTime
    {
        return $this->confirmedAt;
    }

    /**
     * @param \DateTime $confirmedAt
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isConfirmed
     */
    public function setIsConfirmed(?bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * @return bool
     */
    public function isExpired(): ?bool
    {
        return $this->isExpired;
    }

    /**
     * @param bool $isExpired
     */
    public function setIsExpired(?bool $isExpired): void
    {
        $this->isExpired = $isExpired;
    }
}
