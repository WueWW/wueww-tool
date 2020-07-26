<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 */
class Location
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Der Name darf nicht leer sein.", groups={"offline_event"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Straße und Hausnummer dürfen nicht leer sein.", groups={"offline_event"})
     */
    private $streetNo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Die PLZ darf nicht leer sein", groups={"offline_event"})
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Der Ort darf nicht leer sein", groups={"offline_event"})
     */
    private $city;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStreetNo(): ?string
    {
        return $this->streetNo;
    }

    public function setStreetNo(?string $streetNo): self
    {
        $this->streetNo = $streetNo;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
