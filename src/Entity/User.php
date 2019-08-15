<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    const ROLE_EDITOR = 'ROLE_EDITOR';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="owner", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $sessions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="owner", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $tokens;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $registrationComplete;

    /**
     * @var OrganizationDetail
     * @ORM\OneToOne(targetEntity="App\Entity\OrganizationDetail", cascade={"persist", "remove"}, orphanRemoval=false)
     */
    private $proposedOrganizationDetails;

    /**
     * @var OrganizationDetail
     * @ORM\OneToOne(targetEntity="App\Entity\OrganizationDetail", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $acceptedOrganizationDetails;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->tokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setOwner($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getOwner() === $this) {
                $session->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setOwner($this);
        }

        return $this;
    }

    public function createToken(): Token
    {
        $token = Token::generate();
        $this->addToken($token);
        
        return $token;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getOwner() === $this) {
                $token->setOwner(null);
            }
        }

        return $this;
    }

    public function isRegistrationComplete(): bool
    {
        return $this->registrationComplete;
    }

    public function setRegistrationComplete(bool $registrationComplete): self
    {
        $this->registrationComplete = $registrationComplete;

        return $this;
    }

    public function getProposedOrganizationDetails(): ?OrganizationDetail
    {
        return $this->proposedOrganizationDetails;
    }

    public function setProposedOrganizationDetails(?OrganizationDetail $proposedOrganizationDetails): self
    {
        $this->proposedOrganizationDetails = $proposedOrganizationDetails;

        return $this;
    }

    public function getAcceptedOrganizationDetails(): ?OrganizationDetail
    {
        return $this->acceptedOrganizationDetails;
    }

    public function setAcceptedOrganizationDetails(?OrganizationDetail $acceptedOrganizationDetails): self
    {
        $this->acceptedOrganizationDetails = $acceptedOrganizationDetails;

        return $this;
    }

    public function ensureEditableOrganizationDetails(): void
    {
        if ($this->proposedOrganizationDetails === null) {
            $this->proposedOrganizationDetails = new OrganizationDetail();
            return;
        }

        if ($this->proposedOrganizationDetails === $this->acceptedOrganizationDetails) {
            $this->proposedOrganizationDetails = $this->acceptedOrganizationDetails->editableClone();
        }
    }
}
