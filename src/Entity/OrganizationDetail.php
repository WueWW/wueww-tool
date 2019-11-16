<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationDetailRepository")
 */
class OrganizationDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $contactName;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(max = 500, maxMessage = "Die Beschreibung soll max. 500 Zeichen lang sein, da sie für Social Media Zwecke geeignet sein soll")     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitterUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $youtubeUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $instagramUrl;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $logoBlob;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): self
    {
        $this->contactName = $contactName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getFacebookUrl(): ?string
    {
        return $this->facebookUrl;
    }

    public function setFacebookUrl(?string $facebookUrl): self
    {
        $this->facebookUrl = $facebookUrl;

        return $this;
    }

    public function getTwitterUrl(): ?string
    {
        return $this->twitterUrl;
    }

    public function setTwitterUrl(?string $twitterUrl): self
    {
        $this->twitterUrl = $twitterUrl;

        return $this;
    }

    public function getYoutubeUrl(): ?string
    {
        return $this->youtubeUrl;
    }

    public function setYoutubeUrl(?string $youtubeUrl): self
    {
        $this->youtubeUrl = $youtubeUrl;

        return $this;
    }

    public function getInstagramUrl(): ?string
    {
        return $this->instagramUrl;
    }

    public function setInstagramUrl(?string $instagramUrl): self
    {
        $this->instagramUrl = $instagramUrl;

        return $this;
    }

    public function getLogoBlob()
    {
        return $this->logoBlob;
    }

    public function setLogoBlob($logoBlob): self
    {
        $this->logoBlob = $logoBlob;

        return $this;
    }

    public function editableClone(): self
    {
        return (new self())
            ->setTitle($this->getTitle())
            ->setDescription($this->getDescription())
            ->setLink($this->getLink())
            ->setFacebookUrl($this->getFacebookUrl())
            ->setTwitterUrl($this->getTwitterUrl())
            ->setYoutubeUrl($this->getYoutubeUrl())
            ->setInstagramUrl($this->getInstagramUrl())
            ->setLogoBlob($this->getLogoBlob());
    }
}
