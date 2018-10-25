<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="partners")
 * @ORM\Entity(repositoryClass="App\Repository\PartnerRepository")
 */
class Partner
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
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $status;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="partner")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $user;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $country;

    /**
     * @var Location
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $location;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PartnerRequest", mappedBy="partner")
     * @ORM\OrderBy({"createdAt": "DESC"})
     *
     * @JMS\Groups("api_v1")
     */
    private $requests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PartnerPostalCode", mappedBy="partner")
     * @ORM\OrderBy({"createdAt": "DESC"})
     *
     * @JMS\Groups("api_v2")
     */
    private $postalCodes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PartnerSubscription", mappedBy="partner")
     * @ORM\OrderBy({"createdAt": "DESC"})
     *
     * @JMS\Groups("api_v2")
     */
    private $subscriptions;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v2")
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups("api_v2")
     */
    private $accountId;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups("api_v2")
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups("api_v2")
     */
    private $customerResponse;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups("api_v2")
     */
    private $cardToken;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->user = new User();
        $this->status = PartnerStatus::CREATED;
        $this->postalCodes = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
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
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return ArrayCollection
     */
    public function getPostalCodes()
    {
        if (is_null($this->postalCodes)) {
            $this->postalCodes = new ArrayCollection();
        }
        return $this->postalCodes;
    }

    /**
     * @return Country
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return ArrayCollection
     */
    public function getRequests()
    {
        return $this->requests;
    }

    public function addRequest(PartnerRequest $request)
    {
        $this->requests->add($request);
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
    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    /**
     * @param string $accountId
     */
    public function setAccountId(?string $accountId): void
    {
        $this->accountId = $accountId;
    }

    /**
     * @return Location
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function getCustomerResponse(): ?string
    {
        return $this->customerResponse;
    }

    /**
     * @param string $value
     */
    public function setCustomerResponse(?string $value): void
    {
        $this->customerResponse = $value;
    }

    /**
     * @return string
     */
    public function getCardToken(): ?string
    {
        return $this->cardToken;
    }

    /**
     * @param string $cardToken
     */
    public function setCardToken(?string $cardToken): void
    {
        $this->cardToken = $cardToken;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        if (is_null($this->subscriptions)) {
            $this->subscriptions = new ArrayCollection();
        }
        return $this->subscriptions;
    }
}