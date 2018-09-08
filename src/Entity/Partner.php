<?php

namespace App\Entity;

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
     * @var User
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="partner")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var District
     *
     * @ORM\OneToOne(targetEntity="App\Entity\District")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v2")
     */
    private $district;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->user = new User();
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
}