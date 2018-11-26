<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="categories", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unq_categories", columns={"name", "parent_id", "locale"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(type="string", length=4, nullable=false)
     *
     * @JMS\Groups("api_v2")
     */
    private $locale;

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
     * @ORM\Column(type="text", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @JMS\Groups("api_v2")
     */
    private $lvl;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $ordering;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $hasPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $isSelectable;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(nullable=true)
     *
     * @JMS\Groups("api_v2")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @JMS\Groups("api_v1")
     */
    private $children;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->lvl = 0;
        $this->ordering = 0;
        $this->isSelectable = false;
        $this->hasPrice = false;
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return int
     */
    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    /**
     * @param int $lvl
     */
    public function setLvl(?int $lvl): void
    {
        $this->lvl = $lvl;
    }

    /**
     * @return Category
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category $parent
     */
    public function setParent(?Category $parent): void
    {
        $this->parent = $parent;
    }

    public function addChild(Category $item)
    {
        if (is_null($this->children)) {
            $this->children = new ArrayCollection();
        }

        $this->children->add($item);
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
    public function isSelectable(): ?bool
    {
        return $this->isSelectable;
    }

    /**
     * @param bool $isSelectable
     */
    public function setSelectable(?bool $isSelectable): void
    {
        $this->isSelectable = $isSelectable;
    }

    /**
     * @return bool
     */
    public function hasPrice(): ?bool
    {
        return $this->hasPrice;
    }

    /**
     * @param bool $hasPrice
     */
    public function setHasPrice(?bool $hasPrice): void
    {
        $this->hasPrice = $hasPrice;
    }

    /**
     * @return int
     */
    public function getOrdering(): int
    {
        return $this->ordering;
    }

    /**
     * @param int $ordering
     */
    public function setOrdering(int $ordering): void
    {
        $this->ordering = $ordering;
    }
}
