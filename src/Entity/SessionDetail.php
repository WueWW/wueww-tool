<?php

namespace App\Entity;

use App\DTO\SessionWithDetail;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionDetailRepository")
 */
class SessionDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $longDescription;

    /**
     * @ORM\Embedded(class = "Location")
     */
    private $location;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $locationLat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $locationLng;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $onlineOnly;

    /**
     * @ORM\ManyToOne(targetEntity=Channel::class)
     */
    private $channel;

    public function __construct()
    {
        $this->location = new Location();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): self
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    public function setLocationLat(?float $locationLat): self
    {
        $this->locationLat = $locationLat;

        return $this;
    }

    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    public function setLocationLng(?float $locationLng): self
    {
        $this->locationLng = $locationLng;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getOnlineOnly(): ?bool
    {
        return $this->onlineOnly;
    }

    public function setOnlineOnly(bool $onlineOnly): self
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    public function apply(SessionWithDetail $sessionWithDetail)
    {
        $this->setOnlineOnly($sessionWithDetail->getOnlineOnly())
            ->setTitle($sessionWithDetail->getTitle())
            ->setShortDescription($sessionWithDetail->getShortDescription())
            ->setLongDescription($sessionWithDetail->getLongDescription())
            ->setChannel($sessionWithDetail->getChannel())
            ->setLocation($sessionWithDetail->getLocation())
            ->setLocationLat($sessionWithDetail->getLocationLat())
            ->setLocationLng($sessionWithDetail->getLocationLng())
            ->setLink($sessionWithDetail->getLink());
    }

    public function differs(SessionWithDetail $sessionWithDetail): bool
    {
        return $this->getOnlineOnly() !== $sessionWithDetail->getOnlineOnly() ||
            $this->getTitle() !== $sessionWithDetail->getTitle() ||
            $this->getShortDescription() !== $sessionWithDetail->getShortDescription() ||
            $this->getLongDescription() !== $sessionWithDetail->getLongDescription() ||
            $this->getChannel() !== $sessionWithDetail->getChannel() ||
            $this->getLocation() !== $sessionWithDetail->getLocation() ||
            $this->getLocationLat() !== $sessionWithDetail->getLocationLat() ||
            $this->getLocationLng() !== $sessionWithDetail->getLocationLng() ||
            $this->getLink() !== $sessionWithDetail->getLink();
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }
}
