<?php

namespace PhpDockerManager\Exception;

class UnjoinableContainerException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
