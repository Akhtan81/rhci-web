<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="geo_regions")
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region
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
     * @JMS\Groups("api_v1")
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false, unique=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false, unique=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $fullName;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v2")
     */
    private $country;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * @return string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

}