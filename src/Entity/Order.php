<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     *
     * @JMS\Groups("api_v1")
     */
    private $partner;

    /**
     * @var District
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\District")
     * @ORM\JoinColumn(nullable=true)
     *
     * @JMS\Groups("api_v1")
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
    private $isScheduleApproved;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $price;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $repeatable;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="order")
     *
     * @JMS\Groups("api_v1")
     */
    private $messages;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="order")
     *
     * @JMS\Groups("api_v1")
     */
    private $items;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = OrderStatus::CREATED;
        $this->isScheduleApproved = false;
        $this->price = 0;
        $this->messages = new ArrayCollection();
        $this->items = new ArrayCollection();
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
    public function isScheduleApproved(): ?bool
    {
        return $this->isScheduleApproved;
    }

    /**
     * @param bool $value
     */
    public function setIsScheduleApproved(?bool $value): void
    {
        $this->isScheduleApproved = $value;
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
     * @return ArrayCollection
     */
    public function getMessages(): ArrayCollection
    {
        return $this->messages;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }


    public function addItem(OrderItem $item)
    {
        $this->items->add($item);
    }

    public function addMessage(Message $item)
    {
        $this->messages->add($item);
    }
}