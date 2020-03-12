<?php

namespace App\Event;

use App\Entity\Session;
use Symfony\Contracts\EventDispatcher\Event;

class SessionCancelledEvent extends Event
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        if ($session->getId() === null) {
            throw new \InvalidArgumentException('Provided Session instance must be persisted');
        }

        $this->session = $session;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }
}
