<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="units")
 * @ORM\Entity(repositoryClass="App\Repository\UnitRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Unit
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Groups({"api_v1", "api_v2"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @JMS\Groups({"api_v2"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UnitTranslation", mappedBy="unit")
     * @ORM\JoinColumn(nullable=true)
     *
     * @JMS\Groups({"api_v1"})
     */
    private $translations;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->translations = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        if (is_null($this->translations)) {
            $this->translations = new ArrayCollection();
        }
        return $this->translations;
    }

    public function addTranslation(UnitTranslation $entity)
    {
        $this->getTranslations()->add($entity);
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
