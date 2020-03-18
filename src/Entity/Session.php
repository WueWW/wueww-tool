<?php

namespace App\Entity;

use App\DTO\SessionWithDetail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
     */
    private $importKey;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $stop;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $cancelled;

    /**
     * @var ?SessionDetail
     * @ORM\OneToOne(targetEntity="App\Entity\SessionDetail", cascade={"persist", "remove"}, orphanRemoval=false)
     * @ORM\JoinColumn(nullable=false)
     */
    private $proposedDetails;

    /**
     * @var ?SessionDetail
     * @ORM\OneToOne(targetEntity="App\Entity\SessionDetail", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $acceptedDetails;

    /**
     * @var ?Organization
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Feedback", mappedBy="session", orphanRemoval=true)
     */
    private $feedback;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $acceptedAt;

    public function __construct()
    {
        $this->setCancelled(false);
        $this->setProposedDetails(new SessionDetail());
        $this->feedback = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getImportKey(): ?string
    {
        return $this->importKey;
    }

    /**
     * @param string|null $importKey
     * @return Session
     */
    public function setImportKey(?string $importKey): self
    {
        $this->importKey = $importKey;

        return $this;
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

    public function isAccepted(): bool
    {
        return $this->acceptedDetails === $this->proposedDetails;
    }

    public function toSessionWithDetail(): SessionWithDetail
    {
        return (new SessionWithDetail())
            ->setId($this->getId())
            ->setDate($this->getStart())
            ->setStart($this->getStart())
            ->setStop($this->getStop())
            ->setOrganization($this->getOrganization())
            ->setTitle($this->getProposedDetails()->getTitle())
            ->setShortDescription($this->getProposedDetails()->getShortDescription())
            ->setLongDescription($this->getProposedDetails()->getLongDescription())
            ->setLocation($this->getProposedDetails()->getLocation())
            ->setLink($this->getProposedDetails()->getLink());
    }

    public function applyDetails(SessionWithDetail $sessionWithDetail): self
    {
        if ($this->getProposedDetails() === $this->getAcceptedDetails()) {
            $this->setProposedDetails(new SessionDetail());
        }

        $start = (new \DateTime())
            ->setDate(
                (int) $sessionWithDetail->getDate()->format('Y'),
                (int) $sessionWithDetail->getDate()->format('m'),
                (int) $sessionWithDetail->getDate()->format('d')
            )
            ->setTime(
                (int) $sessionWithDetail->getStart()->format('H'),
                (int) $sessionWithDetail->getStart()->format('i')
            );

        $stop =
            $sessionWithDetail->getStop() === null
                ? null
                : (new \DateTime())
                    ->setDate(
                        (int) $sessionWithDetail->getDate()->format('Y'),
                        (int) $sessionWithDetail->getDate()->format('m'),
                        (int) $sessionWithDetail->getDate()->format('d')
                    )
                    ->setTime(
                        (int) $sessionWithDetail->getStop()->format('H'),
                        (int) $sessionWithDetail->getStop()->format('i')
                    );

        $this->setStart($start)
            ->setStop($stop)
            ->setOrganization($sessionWithDetail->getOrganization());

        $this->getProposedDetails()
            ->setTitle($sessionWithDetail->getTitle())
            ->setShortDescription($sessionWithDetail->getShortDescription())
            ->setLongDescription($sessionWithDetail->getLongDescription())
            ->setLocation($sessionWithDetail->getLocation())
            ->setLink($sessionWithDetail->getLink());

        return $this;
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

    /**
     * @return Collection|Feedback[]
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): self
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback[] = $feedback;
            $feedback->setSession($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): self
    {
        if ($this->feedback->contains($feedback)) {
            $this->feedback->removeElement($feedback);
            // set the owning side to null (unless already changed)
            if ($feedback->getSession() === $this) {
                $feedback->setSession(null);
            }
        }

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
