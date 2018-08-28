<?php

namespace App\Entity;

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
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
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
     * @var bool
     *
     * @ORM\Column(name="is_admin", type="boolean", nullable=false)
     */
    private $isAdmin;

    /**
     * @var partner
     *
     * @ORM\OneToOne(targetEntity="App\Entity\partner", mappedBy="user")
     */
    private $partner;

    public function __construct()
    {
        $this->isActive = true;
        $this->isAdmin = false;
        $this->createdAt = new \DateTime();
    }

    public function getPassword(): ?string
    {
        return $this->password;
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
     * @return string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
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
}