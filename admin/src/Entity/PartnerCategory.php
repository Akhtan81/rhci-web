<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="partner_categories", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unq_partner_categories", columns={"partner_id", "category_id", "unit_id", "min_amount", "deleted_at"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\PartnerCategoryRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class PartnerCategory
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
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v2")
     */
    private $partner;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $price;

    /**
     * @var Unit
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Unit")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $unit;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $minAmount;

    /**
     * @var ArrayCollection
     *
     * @JMS\Groups("api_v1")
     */
    private $children;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->children = new ArrayCollection();
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

    public function addChild(PartnerCategory $item)
    {
        if (is_null($this->children)) {
            $this->children = new ArrayCollection();
        }

        $this->children->add($item);
    }

    /**
     * @return Unit
     */
    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    /**
     * @param Unit $unit
     */
    public function setUnit(?Unit $unit): void
    {
        $this->unit = $unit;
    }

    /**
     * @return int
     */
    public function getMinAmount(): ?int
    {
        return $this->minAmount;
    }

    /**
     * @param int $minAmount
     */
    public function setMinAmount(?int $minAmount): void
    {
        $this->minAmount = $minAmount;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        if (is_null($this->children)) {
            $this->children = new ArrayCollection();
        }
        return $this->children;
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
