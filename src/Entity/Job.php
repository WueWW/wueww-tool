<?php

namespace App\Entity;

use App\DTO\JobWithDetail;
use Doctrine\Common\Collections\ArrayCollection;
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

    public function toJobWithDetail(): JobWithDetail
    {
        return (new JobWithDetail())
            ->setId($this->getId())
            ->setOrganization($this->getOrganization())
            ->setTitle($this->getProposedDetails()->getTitle())
            ->setShortDescription($this->getProposedDetails()->getShortDescription())
            ->setLink($this->getProposedDetails()->getLink());
    }

    public function applyDetails(JobWithDetail $jobWithDetail): self
    {
        $this->setOrganization($jobWithDetail->getOrganization());

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
                ->setLink($jobWithDetail->getLink());
        }

        return $this;
    }

    private function differsInDetails(JobWithDetail $jobWithDetail): bool
    {
        $currentDetails = $this->getProposedDetails();

        return $currentDetails->getTitle() !== $jobWithDetail->getTitle() ||
            $currentDetails->getShortDescription() !== $jobWithDetail->getShortDescription() ||
            $currentDetails->getLink() !== $jobWithDetail->getLink();
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
