<?php

namespace App\Entity;

use App\DTO\SessionWithDetail;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $stop;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cancelled;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SessionDetail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $proposedDetails;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SessionDetail", cascade={"persist", "remove"})
     */
    private $acceptedDetails;

    public function __construct()
    {
        $this->setProposedDetails(new SessionDetail());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getStop(): ?\DateTimeInterface
    {
        return $this->stop;
    }

    public function setStop(?\DateTimeInterface $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    public function getCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        $this->cancelled = $cancelled;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getProposedDetails(): ?SessionDetail
    {
        return $this->proposedDetails;
    }

    public function setProposedDetails(SessionDetail $proposedDetails): self
    {
        $this->proposedDetails = $proposedDetails;

        return $this;
    }

    public function getAcceptedDetails(): ?SessionDetail
    {
        return $this->acceptedDetails;
    }

    public function setAcceptedDetails(?SessionDetail $acceptedDetails): self
    {
        $this->acceptedDetails = $acceptedDetails;

        return $this;
    }

    public function toSessionWithDetail(): SessionWithDetail
    {
        return (new SessionWithDetail())
            ->setId($this->getId())
            ->setDate($this->getStart())
            ->setStart($this->getStart())
            ->setStop($this->getStop())
            ->setTitle($this->getProposedDetails()->getTitle())
            ->setShortDescription($this->getProposedDetails()->getShortDescription())
            ->setLongDescription($this->getProposedDetails()->getLongDescription())
            ->setLocationName($this->getProposedDetails()->getLocationName())
            ->setLocationLat($this->getProposedDetails()->getLocationLat())
            ->setLocationLng($this->getProposedDetails()->getLocationLng())
            ->setLink($this->getProposedDetails()->getLink());
    }

    public function applyDetails(SessionWithDetail $sessionWithDetail): self
    {
        if ($this->getProposedDetails() === $this->getAcceptedDetails()) {
            $this->setProposedDetails(new SessionDetail());
        }

        $start = (new \DateTime())
            ->setDate($sessionWithDetail->getDate()->format('Y'), $sessionWithDetail->getDate()->format('m'), $sessionWithDetail->getDate()->format('d'))
            ->setTime($sessionWithDetail->getStart()->format('H'), $sessionWithDetail->getStart()->format('i'));

        $stop = $sessionWithDetail->getStop() === null ? null : (
        (new \DateTime())
            ->setDate($sessionWithDetail->getDate()->format('Y'), $sessionWithDetail->getDate()->format('m'), $sessionWithDetail->getDate()->format('d'))
            ->setTime($sessionWithDetail->getStop()->format('H'), $sessionWithDetail->getStop()->format('i'))
        );

        $this
            ->setStart($start)
            ->setStop($stop);

        $this->getProposedDetails()
            ->setTitle($sessionWithDetail->getTitle())
            ->setShortDescription($sessionWithDetail->getShortDescription())
            ->setLongDescription($sessionWithDetail->getLongDescription())
            ->setLocationName($sessionWithDetail->getLocationName())
            ->setLocationLat($sessionWithDetail->getLocationLat())
            ->setLocationLng($sessionWithDetail->getLocationLng())
            ->setLink($sessionWithDetail->getLink());

        return $this;
    }
}
