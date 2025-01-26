<?php

namespace PhpDockerManager\Case;

enum StatusCase
{
    case CREATED;
    case RUNNING;
    case RESTARTING;
    case EXITED;
    case PAUSED;
    case DEAD;
}
