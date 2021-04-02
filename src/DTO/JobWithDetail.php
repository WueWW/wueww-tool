<?php

namespace App\DTO;

use App\Entity\Location;
use App\Entity\Organization;
use App\Enum\HomeOfficeEnum;
use App\Enum\OeffiErreichbarkeitEnum;
use App\Enum\GehaltsvorstellungEnum;
use App\Enum\SlackTimeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class JobWithDetail
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     * @Assert\NotBlank(message = "Der Titel darf nicht leer sein.")
     * @Assert\Length(max = 30, maxMessage = "Der Titel darf max. 30 Zeichen lang sein.")
     */
    private $title;

    /**
     * @var string|null
     * @Assert\Length(max = 250, maxMessage = "Die Kurzbeschreibung soll max. 250 Zeichen lang sein, da sie für Social Media Zwecke geeignet sein soll.")
     */
    private $shortDescription;

    /**
     * @var Organization|null
     * @Assert\NotNull(message = "Es muss ein Veranstalter ausgewählt sein.")
     */
    private $organization;

    /**
     * @var string|null
     */
    private $link;

    /**
     * @var bool|null
     */
    private $fullyRemote;

    /**
     * @var Location|null
     */
    private $location;

    /**
     * @var float|null
     */
    private $locationLat;

    /**
     * @var float|null
     */
    private $locationLng;

    /**
     * @var HomeOfficeEnum|null
     */
    private $homeOffice;

    /**
     * @var OeffiErreichbarkeitEnum|null
     */
    private $oeffiErreichbarkeit;

    /**
     * @var SlackTimeEnum|null
     */
    private $slackTime;

    /**
     * @var boolean|null
     */
    private $weiterbildungsbudget;

    /**
     * @var GehaltsvorstellungEnum|null
     */
    private $gehaltsvorstellung;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): JobWithDetail
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): JobWithDetail
    {
        $this->title = $title;
        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): JobWithDetail
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): JobWithDetail
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFullyRemote(): ?bool
    {
        return $this->fullyRemote;
    }

    /**
     * @param bool|null $fullyRemote
     * @return JobWithDetail
     */
    public function setFullyRemote(?bool $fullyRemote): JobWithDetail
    {
        $this->fullyRemote = $fullyRemote;
        return $this;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location|null $location
     * @return JobWithDetail
     */
    public function setLocation(?Location $location): JobWithDetail
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    /**
     * @param float|null $locationLat
     * @return JobWithDetail
     */
    public function setLocationLat(?float $locationLat): JobWithDetail
    {
        $this->locationLat = $locationLat;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    /**
     * @param float|null $locationLng
     * @return JobWithDetail
     */
    public function setLocationLng(?float $locationLng): JobWithDetail
    {
        $this->locationLng = $locationLng;
        return $this;
    }

    /**
     * @return HomeOfficeEnum|null
     */
    public function getHomeOffice(): ?HomeOfficeEnum
    {
        return $this->homeOffice;
    }

    /**
     * @param HomeOfficeEnum|null $homeOffice
     * @return JobWithDetail
     */
    public function setHomeOffice(?HomeOfficeEnum $homeOffice): JobWithDetail
    {
        $this->homeOffice = $homeOffice;
        return $this;
    }

    /**
     * @return OeffiErreichbarkeitEnum|null
     */
    public function getOeffiErreichbarkeit(): ?OeffiErreichbarkeitEnum
    {
        return $this->oeffiErreichbarkeit;
    }

    /**
     * @param OeffiErreichbarkeitEnum|null $oeffiErreichbarkeit
     * @return JobWithDetail
     */
    public function setOeffiErreichbarkeit(?OeffiErreichbarkeitEnum $oeffiErreichbarkeit): JobWithDetail
    {
        $this->oeffiErreichbarkeit = $oeffiErreichbarkeit;
        return $this;
    }

    /**
     * @return SlackTimeEnum|null
     */
    public function getSlackTime(): ?SlackTimeEnum
    {
        return $this->slackTime;
    }

    /**
     * @param SlackTimeEnum|null $slackTime
     * @return JobWithDetail
     */
    public function setSlackTime(?SlackTimeEnum $slackTime): JobWithDetail
    {
        $this->slackTime = $slackTime;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWeiterbildungsbudget(): ?bool
    {
        return $this->weiterbildungsbudget;
    }

    /**
     * @param bool|null $weiterbildungsbudget
     * @return JobWithDetail
     */
    public function setWeiterbildungsbudget(?bool $weiterbildungsbudget): JobWithDetail
    {
        $this->weiterbildungsbudget = $weiterbildungsbudget;
        return $this;
    }

    /**
     * @return GehaltsvorstellungEnum|null
     */
    public function getGehaltsvorstellung(): ?GehaltsvorstellungEnum
    {
        return $this->gehaltsvorstellung;
    }

    /**
     * @param GehaltsvorstellungEnum|null $gehaltsvorstellung
     * @return JobWithDetail
     */
    public function setGehaltsvorstellung(?GehaltsvorstellungEnum $gehaltsvorstellung): JobWithDetail
    {
        $this->gehaltsvorstellung = $gehaltsvorstellung;
        return $this;
    }
}
