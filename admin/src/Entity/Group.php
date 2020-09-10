<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Group
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
     * @var \DateTime $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $codename;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $nameEn;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $nameKz;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $nameRu;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $faIconName;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $bidirectional;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $flag1;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->bidirectional = false;
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
    public function getCodename(): ?string
    {
        return $this->codename;
    }

    /**
     * @param string $input
     */
    public function setCodename(?string $input): void
    {
        $this->codename = $input;
    }

    /**
     * @return string
     */
    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    /**
     * @param string $input
     */
    public function setNameEn(?string $input): void
    {
        $this->nameEn = $input;
    }

    /**
     * @return string
     */
    public function getNameKz(): ?string
    {
        return $this->nameKz;
    }

    /**
     * @param string $input
     */
    public function setNameKz(?string $input): void
    {
        $this->nameKz = $input;
    }

    /**
     * @return string
     */
    public function getNameRu(): ?string
    {
        return $this->nameRu;
    }

    /**
     * @param string $input
     */
    public function setNameRu(?string $input): void
    {
        $this->nameRu = $input;
    }

    /**
     * @return string
     */
    public function getFaIconName(): ?string
    {
        return $this->faIconName;
    }

    /**
     * @param string $input
     */
    public function setFaIconName(?string $input): void
    {
        $this->faIconName = $input;
    }

    /**
     * @return bool
     */
    public function getBidirectional(): ?bool
    {
        return $this->bidirectional;
    }

    /**
     * @param bool $input
     */
    public function setBidirectional(?bool $input): void
    {
        $this->bidirectional = $input;
    }

    /**
     * @return bool
     */
    public function getFlag1(): ?bool
    {
        return $this->flag1;
    }

    /**
     * @param bool $input
     */
    public function setFlag1(?bool $input): void
    {
        $this->flag1 = $input;
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
