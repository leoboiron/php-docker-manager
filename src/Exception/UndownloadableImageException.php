<?php

namespace PhpDockerManager\Exception;

class UndownloadableImageException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
