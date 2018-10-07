<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="item_message_media", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unq_item_message_media", columns={"media_id", "message_id"})
 * })
 * @ORM\Entity()
 */
class ItemMessageMedia
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
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Media")
     *
     * @JMS\Groups("api_v1")
     */
    private $media;

    /**
     * @var ItemMessage
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ItemMessage", inversedBy="media")
     *
     * @JMS\Groups("api_v1")
     */
    private $message;

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
     * @return Media
     */
    public function getMedia(): ?Media
    {
        return $this->media;
    }

    /**
     * @param Media $media
     */
    public function setMedia(?Media $media): void
    {
        $this->media = $media;
    }

    /**
     * @return ItemMessage
     */
    public function getMessage(): ?ItemMessage
    {
        return $this->message;
    }

    /**
     * @param ItemMessage $message
     */
    public function setMessage(?ItemMessage $message): void
    {
        $this->message = $message;
    }
}
