<?php

namespace App\Service\Exception;

use Throwable;

class UsernameNotUniqueException extends \RuntimeException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);
    }
}
