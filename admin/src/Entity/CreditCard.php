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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="creditCards")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false, unique=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $lastFour;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12, nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $type;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->provider = PaymentProvider::STRIPE;
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
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider(?string $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getLastFour(): string
    {
        return $this->lastFour;
    }

    /**
     * @param string $lastFour
     */
    public function setLastFour(string $lastFour): void
    {
        $this->lastFour = $lastFour;
    }

    /**
     * @return string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("isPrimary")
     *
     * @JMS\Groups("api_v1")
     *
     * @return bool
     */
    public function serializeIsPrimary()
    {
        $primary = $this->user->getPrimaryCreditCard();

        return $primary && $primary->getId() === $this->getId();
    }
}
