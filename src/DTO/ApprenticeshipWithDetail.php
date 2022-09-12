<?php

namespace App\DTO;

use App\Entity\Location;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ApprenticeshipWithDetail
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var User
     */
    private $owner;

    /**
     * @var Location
     * @Assert\Valid()
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
     * @var string[]|null
     */
    private $jobs;

    /**
     * @var string|null
     * @Assert\Url(message = "Die erfasste URL ist ungültig.")
     */
    private $jobsUrl;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ApprenticeshipWithDetail
    {
        $this->id = $id;
        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): ApprenticeshipWithDetail
    {
        $this->owner = $owner;
        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): ApprenticeshipWithDetail
    {
        $this->location = $location;
        return $this;
    }

    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    public function setLocationLat(?float $locationLat): ApprenticeshipWithDetail
    {
        $this->locationLat = $locationLat;
        return $this;
    }

    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    public function setLocationLng(?float $locationLng): ApprenticeshipWithDetail
    {
        $this->locationLng = $locationLng;
        return $this;
    }

    public function getJobs(): ?array
    {
        return $this->jobs;
    }

    public function setJobs(?array $jobs): ApprenticeshipWithDetail
    {
        $this->jobs = $jobs;
        return $this;
    }

    public function getJobsUrl(): ?string
    {
        return $this->jobsUrl;
    }

    public function setJobsUrl(string $jobsUrl): ApprenticeshipWithDetail
    {
        $this->jobsUrl = $jobsUrl;
        return $this;
    }
}
