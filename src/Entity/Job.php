<?php

namespace App\Entity;

use App\DTO\JobWithDetail;
use App\Enum\HomeOfficeEnum;
use App\Enum\OeffiErreichbarkeitEnum;
use App\Enum\GehaltsvorstellungEnum;
use App\Enum\SlackTimeEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ?JobDetail
     * @ORM\OneToOne(targetEntity="App\Entity\JobDetail", cascade={"persist", "remove"}, orphanRemoval=false)
     * @ORM\JoinColumn(nullable=false)
     */
    private $proposedDetails;

    /**
     * @var ?JobDetail
     * @ORM\OneToOne(targetEntity="App\Entity\JobDetail", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $acceptedDetails;

    /**
     * @var ?Organization
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="jobs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $acceptedAt;

    /**
     * @ORM\Column(type="homeoffice", nullable=true)
     * @var ?HomeOfficeEnum
     */
    private $homeOffice;

    /**
     * @ORM\Column(type="oeffi_erreichbarkeit", nullable=true)
     * @var ?OeffiErreichbarkeitEnum
     */
    private $oeffiErreichbarkeit;

    /**
     * @ORM\Column(type="slack_time", nullable=true)
     * @var ?SlackTimeEnum
     */
    private $slackTime;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var boolean|null
     */
    private $weiterbildungsbudget;

    /**
     * @ORM\Column(type="gehaltsvorstellung", nullable=true)
     * @var GehaltsvorstellungEnum|null
     */
    private $gehaltsvorstellung;

    public function __construct()
    {
        $this->setProposedDetails(new JobDetail());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProposedDetails(): ?JobDetail
    {
        return $this->proposedDetails;
    }

    public function setProposedDetails(JobDetail $proposedDetails): self
    {
        $this->proposedDetails = $proposedDetails;

        return $this;
    }

    public function getAcceptedDetails(): ?JobDetail
    {
        return $this->acceptedDetails;
    }

    public function setAcceptedDetails(?JobDetail $acceptedDetails): self
    {
        $this->acceptedDetails = $acceptedDetails;

        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->acceptedDetails === $this->proposedDetails;
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
     * @return Job
     */
    public function setHomeOffice(?HomeOfficeEnum $homeOffice): self
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
     * @return Job
     */
    public function setOeffiErreichbarkeit(?OeffiErreichbarkeitEnum $oeffiErreichbarkeit): Job
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
     * @return Job
     */
    public function setSlackTime(?SlackTimeEnum $slackTime): Job
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
     * @return Job
     */
    public function setWeiterbildungsbudget(?bool $weiterbildungsbudget): Job
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
     * @return Job
     */
    public function setGehaltsvorstellung(?GehaltsvorstellungEnum $gehaltsvorstellung): Job
    {
        $this->gehaltsvorstellung = $gehaltsvorstellung;
        return $this;
    }

    public function toJobWithDetail(): JobWithDetail
    {
        return (new JobWithDetail())
            ->setId($this->getId())
            ->setOrganization($this->getOrganization())
            ->setTitle($this->getProposedDetails()->getTitle())
            ->setShortDescription($this->getProposedDetails()->getShortDescription())
            ->setLink($this->getProposedDetails()->getLink())
            ->setFullyRemote($this->getProposedDetails()->isFullyRemote())
            ->setLocation($this->getProposedDetails()->getLocation())
            ->setLocationLat($this->getProposedDetails()->getLocationLat())
            ->setLocationLng($this->getProposedDetails()->getLocationLng())
            ->setHomeOffice($this->getHomeOffice())
            ->setOeffiErreichbarkeit($this->getOeffiErreichbarkeit())
            ->setSlackTime($this->getSlackTime())
            ->setWeiterbildungsbudget($this->getWeiterbildungsbudget())
            ->setGehaltsvorstellung($this->getGehaltsvorstellung());
    }

    public function applyDetails(JobWithDetail $jobWithDetail): self
    {
        $this->setOrganization($jobWithDetail->getOrganization())
            ->setHomeOffice($jobWithDetail->getHomeOffice())
            ->setOeffiErreichbarkeit($jobWithDetail->getOeffiErreichbarkeit())
            ->setSlackTime($jobWithDetail->getSlackTime())
            ->setWeiterbildungsbudget($jobWithDetail->getWeiterbildungsbudget())
            ->setGehaltsvorstellung($jobWithDetail->getGehaltsvorstellung());

        if (
            $this->getAcceptedDetails() === null ||
            $this->getProposedDetails() !== $this->getAcceptedDetails() ||
            $this->differsInDetails($jobWithDetail)
        ) {
            if ($this->getProposedDetails() === $this->getAcceptedDetails()) {
                $this->setProposedDetails(new JobDetail());
            }

            $this->getProposedDetails()
                ->setTitle($jobWithDetail->getTitle())
                ->setShortDescription($jobWithDetail->getShortDescription())
                ->setLink($jobWithDetail->getLink())
                ->setFullyRemote($jobWithDetail->getFullyRemote())
                ->setLocation($jobWithDetail->getLocation())
                ->setLocationLat($jobWithDetail->getLocationLat())
                ->setLocationLng($jobWithDetail->getLocationLng());
        }

        return $this;
    }

    private function differsInDetails(JobWithDetail $jobWithDetail): bool
    {
        $currentDetails = $this->getProposedDetails();

        return $currentDetails->getTitle() !== $jobWithDetail->getTitle() ||
            $currentDetails->getShortDescription() !== $jobWithDetail->getShortDescription() ||
            $currentDetails->getLink() !== $jobWithDetail->getLink() ||
            $currentDetails->isFullyRemote() !== $jobWithDetail->getFullyRemote() ||
            $currentDetails->getLocation() !== $jobWithDetail->getLocation() ||
            $currentDetails->getLocationLat() !== $jobWithDetail->getLocationLat() ||
            $currentDetails->getLocationLng() !== $jobWithDetail->getLocationLng();
    }

    public function accept()
    {
        $this->setAcceptedDetails($this->getProposedDetails())->setAcceptedAt(new \DateTimeImmutable('now'));
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

    public function getAcceptedAt(): ?\DateTimeInterface
    {
        return $this->acceptedAt;
    }

    public function setAcceptedAt(?\DateTimeInterface $acceptedAt): self
    {
        $this->acceptedAt = $acceptedAt;

        return $this;
    }
}
