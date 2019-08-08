<?php


namespace App\DTO;


class SessionWithDetail
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTimeInterface
     */
    private $start;

    /**
     * @var \DateTimeInterface|null
     */
    private $stop;

    /**
     * @var boolean|null
     */
    private $cancelled;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $shortDescription;

    /**
     * @var string|null
     */
    private $longDescription;

    /**
     * @var string|null
     */
    private $locationName;

    /**
     * @var float|null
     */
    private $locationLat;

    /**
     * @var float|null
     */
    private $locationLng;

    /**
     * @var string|null
     */
    private $link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): SessionWithDetail
    {
        $this->id = $id;
        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): SessionWithDetail
    {
        $this->start = $start;
        return $this;
    }

    public function getStop(): ?\DateTimeInterface
    {
        return $this->stop;
    }

    public function setStop(?\DateTimeInterface $stop): SessionWithDetail
    {
        $this->stop = $stop;
        return $this;
    }

    public function getCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): SessionWithDetail
    {
        $this->cancelled = $cancelled;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): SessionWithDetail
    {
        $this->title = $title;
        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): SessionWithDetail
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): SessionWithDetail
    {
        $this->longDescription = $longDescription;
        return $this;
    }

    public function getLocationName(): ?string
    {
        return $this->locationName;
    }

    public function setLocationName(string $locationName): SessionWithDetail
    {
        $this->locationName = $locationName;
        return $this;
    }

    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    public function setLocationLat(?float $locationLat): SessionWithDetail
    {
        $this->locationLat = $locationLat;
        return $this;
    }

    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    public function setLocationLng(?float $locationLng): SessionWithDetail
    {
        $this->locationLng = $locationLng;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): SessionWithDetail
    {
        $this->link = $link;
        return $this;
    }
}