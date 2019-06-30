<?php

namespace App\Entity;

use App\Classes\Guid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
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
     * @var string
     *
     * @ORM\Column(type="string", length=40, unique=true, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $guid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $deletedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v2")
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
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $statusReason;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v2")
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
     * @ORM\Column(type="bigint", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $price;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $isPriceApproved;

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
     * @ORM\OrderBy({"createdAt": "ASC"})
     *
     * @JMS\Groups("api_v1")
     */
    private $messages;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="order")
     * @ORM\OrderBy({"createdAt": "ASC"})
     *
     * @JMS\Groups("api_v1")
     */
    private $items;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="order")
     * @ORM\OrderBy({"createdAt": "DESC"})
     *
     * @JMS\Groups("api_v2")
     */
    private $payments;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = OrderStatus::CREATED;
        $this->isScheduleApproved = false;
        $this->isPriceApproved = false;
        $this->price = 0;
        $this->messages = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->guid = Guid::generate();
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
     * @return string
     */
    public function getGuid(): ?string
    {
        return $this->guid;
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
    public function getMessages()
    {
        if (is_null($this->messages)) {
            $this->messages = new ArrayCollection();
        }
        return $this->messages;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        if (is_null($this->items)) {
            $this->items = new ArrayCollection();
        }
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

    /**
     * @return ArrayCollection
     */
    public function getPayments()
    {
        if (is_null($this->payments)) {
            $this->payments = new ArrayCollection();
        }
        return $this->payments;
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
     * @return bool
     */
    public function isPriceApproved(): ?bool
    {
        return $this->isPriceApproved;
    }

    /**
     * @param bool $isPriceApproved
     */
    public function setIsPriceApproved(?bool $isPriceApproved): void
    {
        $this->isPriceApproved = $isPriceApproved;
    }

    /**
     * @return string
     */
    public function getStatusReason(): string
    {
        return $this->statusReason;
    }

    /**
     * @param string $statusReason
     */
    public function setStatusReason(string $statusReason): void
    {
        $this->statusReason = $statusReason;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}