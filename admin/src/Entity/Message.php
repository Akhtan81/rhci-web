<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
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
     *
     * @JMS\Groups("api_v1")
     */
    private $user;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="messages")
     *
     * @JMS\Groups("api_v1")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Groups("api_v1")
     */
    private $text;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\MessageMedia", mappedBy="message")
     */
    private $media;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->media = new ArrayCollection();
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
     * @return Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return ArrayCollection
     */
    public function getMedia()
    {
        if (is_null($this->media)) {
            $this->media = new ArrayCollection();
        }
        return $this->media;
    }

    public function addMedia(MessageMedia $messageMedia)
    {
        $this->media->add($messageMedia);
    }

    /**
     * @return array
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("media")
     *
     * @JMS\Groups("api_v1")
     */
    public function serializeMedia()
    {
        return array_map(function (MessageMedia $messageMedia) {
            return $messageMedia->getMedia();
        }, $this->media->toArray());
    }

}
