<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
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
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner")
     * @ORM\JoinColumn(nullable=true)
     */
    private $partner;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @var PartnerCategory
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PartnerCategory")
     * @ORM\JoinColumn(nullable=true)
     */
    private $partnerCategory;

    /**
     * @var District
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\District")
     * @ORM\JoinColumn(nullable=true)
     */
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $updatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $scheduledAt;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $isScheduledApproved;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=7, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $locationLng;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=7, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $locationLat;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $repeatable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = OrderStatus::CREATED;
        $this->isScheduledApproved = false;
        $this->price = 0;
        $this->quantity = 0;
        $this->locationLat = 0;
        $this->locationLng = 0;
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
     * @return Partner
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     */
    public function setPartner(?Partner $partner): void
    {
        $this->partner = $partner;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return PartnerCategory
     */
    public function getPartnerCategory(): ?PartnerCategory
    {
        return $this->partnerCategory;
    }

    /**
     * @param PartnerCategory $partnerCategory
     */
    public function setPartnerCategory(?PartnerCategory $partnerCategory): void
    {
        $this->partnerCategory = $partnerCategory;
    }

    /**
     * @return District
     */
    public function getDistrict(): ?District
    {
        return $this->district;
    }

    /**
     * @param District $district
     */
    public function setDistrict(?District $district): void
    {
        $this->district = $district;
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
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return User
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(?User $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return \DateTime
     */
    public function getScheduledAt(): ?\DateTime
    {
        return $this->scheduledAt;
    }

    /**
     * @param \DateTime $scheduledAt
     */
    public function setScheduledAt(?\DateTime $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }

    /**
     * @return bool
     */
    public function isScheduledApproved(): ?bool
    {
        return $this->isScheduledApproved;
    }

    /**
     * @param bool $isScheduledApproved
     */
    public function setIsScheduledApproved(?bool $isScheduledApproved): void
    {
        $this->isScheduledApproved = $isScheduledApproved;
    }

    /**
     * @return int
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return float
     */
    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    /**
     * @param float $locationLng
     */
    public function setLocationLng(?float $locationLng): void
    {
        $this->locationLng = $locationLng;
    }

    /**
     * @return float
     */
    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    /**
     * @param float $locationLat
     */
    public function setLocationLat(?float $locationLat): void
    {
        $this->locationLat = $locationLat;
    }

    /**
     * @return string
     */
    public function getRepeatable(): ?string
    {
        return $this->repeatable;
    }

    /**
     * @param string $repeatable
     */
    public function setRepeatable(?string $repeatable): void
    {
        $this->repeatable = $repeatable;
    }

}