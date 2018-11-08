<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="item_messages")
 * @ORM\Entity()
 */
class ItemMessage
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
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $user;

    /**
     * @var OrderItem
     *
     * @ORM\OneToOne(targetEntity="App\Entity\OrderItem", inversedBy="message")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMS\Groups("api_v1")
     */
    private $item;

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
     * @ORM\OneToMany(targetEntity="App\Entity\ItemMessageMedia", mappedBy="message")
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
     * @return OrderItem
     */
    public function getItem(): ?OrderItem
    {
        return $this->item;
    }

    /**
     * @param OrderItem $item
     */
    public function setItem(?OrderItem $item): void
    {
        $this->item = $item;
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

    public function addMedia(ItemMessageMedia $messageMedia)
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
        return array_map(function (ItemMessageMedia $messageMedia) {
            return $messageMedia->getMedia();
        }, $this->media->toArray());
    }

}
