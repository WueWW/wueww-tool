<?php

namespace App\Event;

use App\Entity\Apprenticeship;
use Symfony\Contracts\EventDispatcher\Event;

class ApprenticeshipModifiedEvent extends Event
{
    private Apprenticeship $apprenticeship;

    public function __construct(Apprenticeship $apprenticeship)
    {
        if ($apprenticeship->getId() === null) {
            throw new \InvalidArgumentException('Provided Apprenticeship instance must be persisted');
        }

        $this->apprenticeship = $apprenticeship;
    }

    public function getApprenticeship(): Apprenticeship
    {
        return $this->apprenticeship;
    }
}
