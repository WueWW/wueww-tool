<?php

namespace App\Entity;

use App\DTO\ApprenticeshipWithDetail;
use App\Repository\ApprenticeshipDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApprenticeshipDetailRepository::class)
 */
class ApprenticeshipDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Embedded(class = "Location")
     */
    private $location;

    /**
     * @ORM\Column(type="float")
     */
    private $locationLat;

    /**
     * @ORM\Column(type="float")
     */
    private $locationLng;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $jobs = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function setLocationLat(float $locationLat): self
    {
        $this->locationLat = $locationLat;

        return $this;
    }

    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    public function setLocationLng(float $locationLng): self
    {
        $this->locationLng = $locationLng;

        return $this;
    }

    public function getJobs(): ?array
    {
        return $this->jobs;
    }

    public function setJobs(array $jobs): self
    {
        $this->jobs = $jobs;

        return $this;
    }

    public function differs(ApprenticeshipWithDetail $apprenticeshipWithDetail): bool
    {
        return $this->getLocation() !== $apprenticeshipWithDetail->getLocation() ||
            $this->getLocationLat() !== $apprenticeshipWithDetail->getLocationLat() ||
            $this->getLocationLng() !== $apprenticeshipWithDetail->getLocationLng() ||
            $this->getJobs() !== $apprenticeshipWithDetail->getJobs();
    }

    public function apply(ApprenticeshipWithDetail $apprenticeshipWithDetail)
    {
        $this->setLocation($apprenticeshipWithDetail->getLocation())
            ->setLocationLat($apprenticeshipWithDetail->getLocationLat())
            ->setLocationLng($apprenticeshipWithDetail->getLocationLng())
            ->setJobs($apprenticeshipWithDetail->getJobs());
    }
}
