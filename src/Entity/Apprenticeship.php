<?php

namespace App\Entity;

use App\DTO\ApprenticeshipWithDetail;
use App\Repository\ApprenticeshipRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApprenticeshipRepository::class)
 */
class Apprenticeship
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToOne(targetEntity=ApprenticeshipDetail::class, cascade={"persist", "remove"}, orphanRemoval=false)
     * @ORM\JoinColumn(nullable=false)
     * @var ApprenticeshipDetail
     */
    private $proposedDetails;

    /**
     * @ORM\OneToOne(targetEntity=ApprenticeshipDetail::class, cascade={"persist", "remove"}, orphanRemoval=false)
     * @var ApprenticeshipDetail|null
     */
    private $acceptedDetails;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProposedDetails(): ?ApprenticeshipDetail
    {
        return $this->proposedDetails;
    }

    public function setProposedDetails(ApprenticeshipDetail $proposedDetails): self
    {
        $this->proposedDetails = $proposedDetails;

        return $this;
    }

    public function getAcceptedDetails(): ?ApprenticeshipDetail
    {
        return $this->acceptedDetails;
    }

    public function setAcceptedDetails(?ApprenticeshipDetail $acceptedDetails): self
    {
        $this->acceptedDetails = $acceptedDetails;

        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->acceptedDetails === $this->proposedDetails && $this->acceptedDetails !== null;
    }

    public function accept()
    {
        $this->setAcceptedDetails($this->getProposedDetails());
    }

    public function applyDetails(ApprenticeshipWithDetail $apprenticeshipWithDetail): self
    {
        if ($this->getProposedDetails() !== null && !$this->getProposedDetails()->differs($apprenticeshipWithDetail)) {
            return $this;
        }

        if ($this->getProposedDetails() === $this->getAcceptedDetails()) {
            $this->setProposedDetails(new ApprenticeshipDetail());
        }

        $this->getProposedDetails()->apply($apprenticeshipWithDetail);

        return $this;
    }

    public function toApprenticeshipWithDetail()
    {
        $details = $this->getProposedDetails();

        return (new ApprenticeshipWithDetail())
            ->setLocation($details->getLocation())
            ->setLocationLat($details->getLocationLat())
            ->setLocationLng($details->getLocationLng())
            ->setJobs($details->getJobs());
    }
}
