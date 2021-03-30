<?php

namespace App\Event;

use App\Entity\Job;
use Symfony\Contracts\EventDispatcher\Event;

class JobDeletedEvent extends Event
{
    /**
     * @var Job
     */
    private $job;

    public function __construct(Job $job)
    {
        if ($job->getId() === null) {
            throw new \InvalidArgumentException('Provided Job instance must be persisted');
        }

        $this->job = $job;
    }

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }
}
