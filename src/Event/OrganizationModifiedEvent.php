<?php

namespace App\Event;

use App\Entity\Organization;
use Symfony\Contracts\EventDispatcher\Event;

class OrganizationModifiedEvent extends Event
{
    /**
     * @var Organization
     */
    private $organization;

    public function __construct(Organization $organization)
    {
        if ($organization->getId() === null) {
            throw new \InvalidArgumentException('Provided Organization instance must be persisted');
        }

        $this->organization = $organization;
    }

    /**
     * @return Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }
}
