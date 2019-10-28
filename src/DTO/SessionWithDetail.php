<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SessionWithDetail
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTimeInterface
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @var \DateTimeInterface
     * @Assert\NotBlank()
     */
    private $start;

    /**
     * @var \DateTimeInterface|null
     */
    private $stop;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Length(max = 30, maxMessage = "Der Titel darf max. 30 Zeichen lang sein")
     */
    private $title;

    /**
     * @var string|null
     * @Assert\Length(max = 250, maxMessage = "Die Kurzbeschreibung soll max. 250 Zeichen lang sein, da sie für Social Media Zwecke geeignet sein soll")
     */
    private $shortDescription;

    /**
     * @var string|null
     */
    private $longDescription;

    /**
     * @var string|null
     * @Assert\NotBlank()
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): SessionWithDetail
    {
        $this->date = $date;
        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): SessionWithDetail
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