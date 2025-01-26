<?php

namespace PhpDockerManager\Exception;

class UnstartableContainerException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
