<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobDetailRepository")
 */
class JobDetail
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var bool|null
     */
    private $fullyRemote;

    /**
     * @ORM\Embedded(class = "Location")
     * @var Location|null
     */
    private $location;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var float|null
     */
    private $locationLat;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var float|null
     */
    private $locationLng;

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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function isFullyRemote(): ?bool
    {
        return $this->fullyRemote;
    }

    public function setFullyRemote(?bool $fullyRemote): self
    {
        $this->fullyRemote = $fullyRemote;
        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
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
}
