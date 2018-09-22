<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Media")
     * @ORM\JoinColumn(nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $avatar;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $isActive;

    /**
     * @var UserLocation
     *
     * @ORM\OneToOne(targetEntity="App\Entity\UserLocation")
     * @ORM\JoinColumn(nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $location;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserLocation", mappedBy="user")
     * @ORM\OrderBy({"createdAt": "DESC"})
     *
     * @JMS\Groups("api_v1_user")
     */
    private $locations;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_admin", type="boolean", nullable=false)
     *
     * @JMS\Groups("api_v2")
     */
    private $isAdmin;

    /**
     * @var CreditCard
     *
     * @ORM\OneToOne(targetEntity="App\Entity\CreditCard")
     * @ORM\JoinColumn(nullable=true)
     *
     * @JMS\Groups("api_v1_user")
     */
    private $primaryCreditCard;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CreditCard", mappedBy="user")
     *
     * @JMS\Groups("api_v1_user")
     */
    private $creditCards;

    /**
     * @var Partner
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Partner", mappedBy="user")
     *
     * @JMS\Groups("api_v2")
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, unique=true, nullable=false)
     */
    private $accessToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $tokenExpiresAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, unique=true, nullable=true)
     */
    private $passwordToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordTokenExpiresAt;

    public function __construct()
    {
        $this->isActive = false;
        $this->isAdmin = false;
        $this->createdAt = new \DateTime();
        $this->locations = new ArrayCollection();
        $this->creditCards = new ArrayCollection();

        $this->refreshToken();
    }

    public function refreshToken()
    {
        $this->accessToken = hash('sha256', uniqid());
        $this->tokenExpiresAt = new \DateTime();
        $this->tokenExpiresAt->modify("+24 hours");
    }

    public function refreshPasswordToken()
    {
        $this->passwordToken = hash('sha256', uniqid());
        $this->passwordTokenExpiresAt = new \DateTime();
        $this->passwordTokenExpiresAt->modify("+24 hours");
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRoles()
    {
        $roles = [Role::USER];

        if ($this->isAdmin) {
            $roles[] = Role::ADMIN;
        }

        if ($this->partner) {
            $roles[] = Role::PARTNER;
        }

        return $roles;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    public function getEmail(): ?string
    {
        return $this->email;
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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function getUsername(): ?string
    {
        if ($this->email) return $this->email;
        return $this->phone;
    }

    /**
     * @return Partner
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    /**
     * @return Media
     */
    public function getAvatar(): ?Media
    {
        return $this->avatar;
    }

    /**
     * @param Media $avatar
     */
    public function setAvatar(?Media $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return UserLocation
     */
    public function getLocation(): ?UserLocation
    {
        return $this->location;
    }

    /**
     * @param UserLocation $location
     */
    public function setLocation(?UserLocation $location): void
    {
        $this->location = $location;
    }

    /**
     * @return CreditCard
     */
    public function getPrimaryCreditCard(): ?CreditCard
    {
        return $this->primaryCreditCard;
    }

    /**
     * @param CreditCard $primaryCreditCard
     */
    public function setPrimaryCreditCard(?CreditCard $primaryCreditCard): void
    {
        $this->primaryCreditCard = $primaryCreditCard;
    }

    /**
     * @return ArrayCollection
     */
    public function getCreditCards()
    {
        if (is_null($this->creditCards)) {
            $this->creditCards = new ArrayCollection();
        }
        return $this->creditCards;
    }

    /**
     * @return ArrayCollection
     */
    public function getLocations()
    {
        if (is_null($this->locations)) {
            $this->locations = new ArrayCollection();
        }
        return $this->locations;
    }

    /**
     * @return \DateTime
     */
    public function getTokenExpiresAt(): ?\DateTime
    {
        return $this->tokenExpiresAt;
    }

    /**
     * @return string
     */
    public function getPasswordToken(): ?string
    {
        return $this->passwordToken;
    }

    /**
     * @param string $passwordToken
     */
    public function setPasswordToken(?string $passwordToken): void
    {
        $this->passwordToken = $passwordToken;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordTokenExpiresAt(): ?\DateTime
    {
        return $this->passwordTokenExpiresAt;
    }

    /**
     * @param \DateTime $passwordTokenExpiresAt
     */
    public function setPasswordTokenExpiresAt(?\DateTime $passwordTokenExpiresAt): void
    {
        $this->passwordTokenExpiresAt = $passwordTokenExpiresAt;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {

    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->phone,
            $this->email,
            $this->password,
            $this->isActive,
            $this->isAdmin,
        ]);
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->phone,
            $this->email,
            $this->password,
            $this->isActive,
            $this->isAdmin,
            ) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getUsername() . '';
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("name")
     *
     * @JMS\Groups("api_v1")
     *
     * @return string
     */
    public function serializeName(): ?string
    {
        if ($this->name) return $this->name;
        if ($this->phone) return $this->phone;
        return $this->email;
    }
}