<?php

namespace PhpDockerManager\Exception;

class UncreatableContainerException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
